<?php
namespace App\Http\Controllers;

use App\Enums\LeaseStatus;
use App\Enums\PropertyStatus;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Support\PaymentMonthStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $period = in_array($request->query('periode'), ['mois', 'trimestre', 'annee'], true)
            ? $request->query('periode')
            : 'mois';

        [$start, $end] = $this->periodBounds($period);
        $today = now();

        $propertiesTotal = Property::count();
        $occupiedCount = Property::where('status', PropertyStatus::Occupied->value)->count();
        $occupancyRate = $propertiesTotal > 0 ? (int) round($occupiedCount / $propertiesTotal * 100) : 0;

        $revenue = \App\Models\Payment::whereBetween('paid_on', [$start, $end])->sum('amount');

        $activeLeases = Lease::query()
            ->with(['property', 'tenant', 'payments'])
            ->where('status', LeaseStatus::Active->value)
            ->get();

        $unpaid = $activeLeases
            ->map(function (Lease $lease) use ($today) {
                $month = $today->copy()->startOfMonth();
                $paidThisMonth = $lease->payments->filter(fn ($p) => $p->month->isSameMonth($month))->sum('amount');
                $status = PaymentMonthStatus::for($lease->monthly_rent, $paidThisMonth, $month, $lease->due_day, $today);
                $dueDate = $month->copy()->day(min($lease->due_day, $month->daysInMonth));

                return ['lease' => $lease, 'status' => $status, 'due_date' => $dueDate];
            })
            ->filter(fn (array $row) => in_array($row['status'], ['late', 'unpaid'], true))
            ->sortBy(fn (array $row) => $row['due_date']->timestamp)
            ->values();

        $dueSoon = $activeLeases
            ->map(function (Lease $lease) use ($today) {
                $dueDate = $today->copy()->startOfMonth()->day(min($lease->due_day, $today->daysInMonth));
                $daysUntilDue = (int) $today->copy()->startOfDay()->diffInDays($dueDate->copy()->startOfDay(), false);

                return ['lease' => $lease, 'due_date' => $dueDate, 'days_until_due' => $daysUntilDue];
            })
            ->filter(fn (array $row) => abs($row['days_until_due']) <= 5)
            ->filter(function (array $row) use ($today) {
                $month = $today->copy()->startOfMonth();
                $paid = $row['lease']->payments->filter(fn ($p) => $p->month->isSameMonth($month))->sum('amount');
                return $paid < $row['lease']->monthly_rent; // pas d'alerte si déjà payé
            })
            ->map(function (array $row) {
                $row['label'] = match (true) {
                    $row['days_until_due'] > 0 => 'Loyer arrive à échéance',
                    $row['days_until_due'] === 0 => 'Loyer dû aujourd\'hui',
                    $row['days_until_due'] >= -4 => 'Retard de paiement',
                    default => 'Loyer impayé',
                };
                return $row;
            })
            ->sortBy(fn (array $row) => $row['days_until_due'])
            ->values();

        $windowStart = now()->copy()->subMonths(11)->startOfMonth();
        $windowEnd = now()->copy()->endOfMonth();

        $paidByMonthKey = \App\Models\Payment::whereBetween('paid_on', [$windowStart, $windowEnd])
            ->get(['paid_on', 'amount'])
            ->groupBy(fn ($payment) => $payment->paid_on->format('Y-m'))
            ->map(fn ($group) => (int) $group->sum('amount'));

        $revenueByMonth = collect(range(11, 0))->map(function (int $monthsAgo) use ($paidByMonthKey) {
            $month = now()->copy()->subMonths($monthsAgo)->startOfMonth();

            return [
                'label' => ucfirst($month->translatedFormat('M Y')),
                'total' => (int) ($paidByMonthKey->get($month->format('Y-m')) ?? 0),
            ];
        });

        $revenueByCommune = \App\Models\Payment::query()
            ->join('leases', 'leases.id', '=', 'payments.lease_id')
            ->join('properties', 'properties.id', '=', 'leases.property_id')
            ->whereBetween('payments.paid_on', [$start, $end])
            ->selectRaw('properties.commune as commune, sum(payments.amount) as total')
            ->groupBy('properties.commune')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => ['commune' => $row->commune, 'total' => (int) $row->total]);

        $vehiclesTotal = Vehicle::count();
        $vehiclesStockValue = (int) Vehicle::query()
            ->selectRaw('COALESCE(SUM(price * stock_quantity), 0) as total')->value('total');
        $vehiclesOutOfStock = Vehicle::where('stock_quantity', 0)->count();
        $vehiclesByType = VehicleType::withCount('vehicles')->orderByDesc('vehicles_count')->get()
            ->map(fn (VehicleType $t) => ['label' => $t->name, 'total' => $t->vehicles_count]);

        return view('dashboard.index', [
            'period' => $period,
            'propertiesTotal' => $propertiesTotal,
            'occupiedCount' => $occupiedCount,
            'occupancyRate' => $occupancyRate,
            'revenue' => $revenue,
            'unpaid' => $unpaid,
            'dueSoon' => $dueSoon,
            'revenueByMonth' => $revenueByMonth,
            'revenueByCommune' => $revenueByCommune,
            'vehiclesTotal' => $vehiclesTotal,
            'vehiclesStockValue' => $vehiclesStockValue,
            'vehiclesOutOfStock' => $vehiclesOutOfStock,
            'vehiclesByType' => $vehiclesByType,
        ]);
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function periodBounds(string $period): array
    {
        $now = now();

        return match ($period) {
            'trimestre' => [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()],
            'annee' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }
}

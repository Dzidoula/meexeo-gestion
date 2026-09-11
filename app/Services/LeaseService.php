<?php
// app/Services/LeaseService.php
namespace App\Services;

use App\Enums\LeaseStatus;
use App\Enums\PropertyStatus;
use App\Models\Lease;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class LeaseService
{
    /**
     * Crée un bail et marque le bien occupé, en une seule transaction :
     * un bien occupé sans bail, ou l'inverse, serait incohérent.
     */
    public function create(array $data): Lease
    {
        return DB::transaction(function () use ($data) {
            $lease = Lease::create([...$data, 'status' => LeaseStatus::Active]);

            $lease->property->update(['status' => PropertyStatus::Occupied]);

            return $lease;
        });
    }

    public function end(Lease $lease, string $actualEndDate): Lease
    {
        return DB::transaction(function () use ($lease, $actualEndDate) {
            $lease->update([
                'status' => LeaseStatus::Ended,
                'actual_end_date' => $actualEndDate,
            ]);

            // Un bien en travaux garde son état : la fin d'un bail ne le rend pas louable.
            if ($lease->property->status === PropertyStatus::Occupied) {
                $lease->property->update(['status' => PropertyStatus::Vacant]);
            }

            return $lease->refresh();
        });
    }

    public function hasActiveLease(Property $property): bool
    {
        return $property->leases()->where('status', LeaseStatus::Active->value)->exists();
    }
}

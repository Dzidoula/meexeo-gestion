<?php

namespace App\Support;

use App\Exceptions\VehicleOutOfStockException;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Cart
{
    /** @return Collection<int, Vehicle> */
    public static function items(): Collection
    {
        if ($customer = Auth::guard('customer')->user()) {
            return CartItem::with('vehicle.vehicleType', 'vehicle.photos')
                ->where('customer_id', $customer->id)
                ->get()
                ->pluck('vehicle');
        }

        return Vehicle::with('vehicleType', 'photos')
            ->whereIn('id', session('cart', []))
            ->get();
    }

    public static function count(): int
    {
        if ($customer = Auth::guard('customer')->user()) {
            return CartItem::where('customer_id', $customer->id)->count();
        }

        return count(session('cart', []));
    }

    /** @throws VehicleOutOfStockException */
    public static function add(Vehicle $vehicle): bool
    {
        if ($vehicle->stock_quantity <= 0) {
            throw new VehicleOutOfStockException();
        }

        if ($customer = Auth::guard('customer')->user()) {
            $item = CartItem::firstOrCreate(['customer_id' => $customer->id, 'vehicle_id' => $vehicle->id]);

            return $item->wasRecentlyCreated;
        }

        $cart = session('cart', []);

        if (in_array($vehicle->id, $cart, true)) {
            return false;
        }

        $cart[] = $vehicle->id;
        session(['cart' => $cart]);

        return true;
    }

    public static function remove(int $vehicleId): void
    {
        if ($customer = Auth::guard('customer')->user()) {
            CartItem::where('customer_id', $customer->id)->where('vehicle_id', $vehicleId)->delete();

            return;
        }

        $cart = array_values(array_diff(session('cart', []), [$vehicleId]));
        session(['cart' => $cart]);
    }

    public static function mergeIntoCustomer(Customer $customer): void
    {
        $vehicleIds = session('cart', []);

        if ($vehicleIds !== []) {
            $rows = array_map(static fn (int $id) => [
                'customer_id' => $customer->id,
                'vehicle_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ], $vehicleIds);

            CartItem::insertOrIgnore($rows);
        }

        session()->forget('cart');
    }
}

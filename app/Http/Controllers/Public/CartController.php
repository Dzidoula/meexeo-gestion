<?php

namespace App\Http\Controllers\Public;

use App\Exceptions\VehicleOutOfStockException;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(): View
    {
        return view('public.cart.show', ['vehicles' => Cart::items()]);
    }

    public function add(Vehicle $vehicle): RedirectResponse
    {
        try {
            $added = Cart::add($vehicle);
        } catch (VehicleOutOfStockException) {
            return back()->with('cart_error', 'Ce véhicule est épuisé et ne peut pas être ajouté au panier.');
        }

        return back()->with('cart_status', $added ? 'Véhicule ajouté au panier.' : 'Déjà dans votre panier.');
    }

    public function remove(Vehicle $vehicle): RedirectResponse
    {
        Cart::remove($vehicle->id);

        return back()->with('cart_status', 'Véhicule retiré du panier.');
    }
}

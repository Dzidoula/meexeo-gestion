<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Customer\AccountController as CustomerAccountController;
use App\Http\Controllers\Customer\Auth\LoginController as CustomerLoginController;
use App\Http\Controllers\Customer\Auth\RegisterController as CustomerRegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HotelRoomController;
use App\Http\Controllers\HotelRoomTypeController;
use App\Http\Controllers\HotelStayController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\MasterclaysAdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPhotoController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\ComingSoonController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\VehicleController as PublicVehicleController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyDocumentController;
use App\Http\Controllers\PropertyPhotoController;
use App\Http\Controllers\StaticModuleController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantDocumentController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehiclePhotoController;
use App\Http\Controllers\VehicleTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/connexion', [LoginController::class, 'show'])->name('login');
Route::post('/connexion', [LoginController::class, 'store'])->name('login.store');
Route::post('/deconnexion', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// MASTERCLAYS — vitrine publique (sans authentification), distincte de l'espace admin ci-dessous.
Route::get('/', [HomeController::class, 'index'])->name('public.home');
Route::get('/nos-vehicules', [PublicVehicleController::class, 'index'])->name('public.vehicles.index');
Route::get('/nos-vehicules/{vehicle}', [PublicVehicleController::class, 'show'])->name('public.vehicles.show');
Route::get('/taxis', [ComingSoonController::class, 'show'])->name('public.taxis')->defaults('activity', 'taxis');
Route::get('/sonorisation', [ComingSoonController::class, 'show'])->name('public.sonorisation')->defaults('activity', 'sonorisation');
Route::get('/podiums', [ComingSoonController::class, 'show'])->name('public.podiums')->defaults('activity', 'podiums');

// MASTERCLAYS — compte client, guard « customer » distinct de l'admin.
Route::get('/inscription', [CustomerRegisterController::class, 'show'])->name('customer.register');
Route::post('/inscription', [CustomerRegisterController::class, 'store'])->name('customer.register.store');
Route::get('/connexion-client', [CustomerLoginController::class, 'show'])->name('customer.login');
Route::post('/connexion-client', [CustomerLoginController::class, 'store'])->name('customer.login.store');
Route::post('/deconnexion-client', [CustomerLoginController::class, 'destroy'])->middleware('auth:customer')->name('customer.logout');

Route::middleware('auth:customer')->group(function () {
    Route::get('/mon-compte', [CustomerAccountController::class, 'show'])->name('customer.account');
});

Route::post('/panier/ajouter/{vehicle}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/panier/{vehicle}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/panier', [CartController::class, 'show'])->name('cart.show');

Route::middleware('auth')->group(function () {
    Route::get('/tableau-de-bord', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/biens', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/locataires', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
    Route::get('/types-vehicules', [VehicleTypeController::class, 'index'])->name('vehicle-types.index');
    Route::get('/types-chambres', [HotelRoomTypeController::class, 'index'])->name('hotel-room-types.index');
    Route::get('/vehicules', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/chambres', [HotelRoomController::class, 'index'])->name('hotel-rooms.index');
    Route::get('/sejours', [HotelStayController::class, 'index'])->name('hotel-stays.index');

    Route::get('/modules/{module}', [StaticModuleController::class, 'show'])->name('modules.show');

    Route::get('/comptabilite', [MasterclaysAdminController::class, 'finance'])->name('masterclays.finance');
    Route::get('/rapports', [MasterclaysAdminController::class, 'reports'])->name('masterclays.reports');
    Route::get('/permissions', [MasterclaysAdminController::class, 'permissions'])->name('masterclays.permissions');
    Route::get('/notifications', [MasterclaysAdminController::class, 'notifications'])->name('masterclays.notifications');
    Route::get('/securite', [MasterclaysAdminController::class, 'security'])->name('masterclays.security');
    Route::get('/parametres', [MasterclaysAdminController::class, 'settings'])->name('masterclays.settings');

    Route::middleware('role:manager')->group(function () {
        Route::get('/biens/nouveau', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/biens', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/biens/{property}/modifier', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/biens/{property}', [PropertyController::class, 'update'])->name('properties.update');

        Route::post('/biens/{property}/photos', [PropertyPhotoController::class, 'store'])->name('properties.photos.store');
        Route::patch('/biens/{property}/photos/{photo}/principale', [PropertyPhotoController::class, 'primary'])->name('properties.photos.primary');
        Route::delete('/biens/{property}/photos/{photo}', [PropertyPhotoController::class, 'destroy'])->name('properties.photos.destroy');

        Route::post('/biens/{property}/documents', [PropertyDocumentController::class, 'store'])->name('properties.documents.store');
        Route::delete('/biens/{property}/documents/{document}', [PropertyDocumentController::class, 'destroy'])->name('properties.documents.destroy');

        Route::get('/locataires/nouveau', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/locataires', [TenantController::class, 'store'])->name('tenants.store');
        Route::get('/locataires/{tenant}/modifier', [TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/locataires/{tenant}', [TenantController::class, 'update'])->name('tenants.update');

        Route::post('/locataires/{tenant}/documents', [TenantDocumentController::class, 'store'])->name('tenants.documents.store');
        Route::delete('/locataires/{tenant}/documents/{document}', [TenantDocumentController::class, 'destroy'])->name('tenants.documents.destroy');

        Route::get('/affectations/nouvelle', [LeaseController::class, 'create'])->name('leases.create');
        Route::post('/affectations', [LeaseController::class, 'store'])->name('leases.store');
        Route::patch('/affectations/{lease}/fin', [LeaseController::class, 'end'])->name('leases.end');

        Route::get('/categories/nouvelle', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/modifier', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/types-vehicules/nouveau', [VehicleTypeController::class, 'create'])->name('vehicle-types.create');
        Route::post('/types-vehicules', [VehicleTypeController::class, 'store'])->name('vehicle-types.store');
        Route::get('/types-vehicules/{vehicleType}/modifier', [VehicleTypeController::class, 'edit'])->name('vehicle-types.edit');
        Route::put('/types-vehicules/{vehicleType}', [VehicleTypeController::class, 'update'])->name('vehicle-types.update');
        Route::delete('/types-vehicules/{vehicleType}', [VehicleTypeController::class, 'destroy'])->name('vehicle-types.destroy');

        Route::get('/types-chambres/nouveau', [HotelRoomTypeController::class, 'create'])->name('hotel-room-types.create');
        Route::post('/types-chambres', [HotelRoomTypeController::class, 'store'])->name('hotel-room-types.store');
        Route::get('/types-chambres/{hotelRoomType}/modifier', [HotelRoomTypeController::class, 'edit'])->name('hotel-room-types.edit');
        Route::put('/types-chambres/{hotelRoomType}', [HotelRoomTypeController::class, 'update'])->name('hotel-room-types.update');
        Route::delete('/types-chambres/{hotelRoomType}', [HotelRoomTypeController::class, 'destroy'])->name('hotel-room-types.destroy');

        Route::get('/chambres/nouveau', [HotelRoomController::class, 'create'])->name('hotel-rooms.create');
        Route::post('/chambres', [HotelRoomController::class, 'store'])->name('hotel-rooms.store');
        Route::get('/chambres/{room}/modifier', [HotelRoomController::class, 'edit'])->name('hotel-rooms.edit');
        Route::put('/chambres/{room}', [HotelRoomController::class, 'update'])->name('hotel-rooms.update');

        Route::get('/sejours/nouveau', [HotelStayController::class, 'create'])->name('hotel-stays.create');
        Route::post('/sejours', [HotelStayController::class, 'store'])->name('hotel-stays.store');
        Route::patch('/sejours/{stay}/arrivee', [HotelStayController::class, 'checkIn'])->name('hotel-stays.check-in');
        Route::patch('/sejours/{stay}/depart', [HotelStayController::class, 'checkOut'])->name('hotel-stays.check-out');
        Route::patch('/sejours/{stay}/annuler', [HotelStayController::class, 'cancel'])->name('hotel-stays.cancel');

        Route::get('/vehicules/nouveau', [VehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicules', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/vehicules/{vehicle}/modifier', [VehicleController::class, 'edit'])->name('vehicles.edit');
        Route::put('/vehicules/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');

        Route::post('/vehicules/{vehicle}/photos', [VehiclePhotoController::class, 'store'])->name('vehicles.photos.store');
        Route::patch('/vehicules/{vehicle}/photos/{photo}/principale', [VehiclePhotoController::class, 'primary'])->name('vehicles.photos.primary');
        Route::delete('/vehicules/{vehicle}/photos/{photo}', [VehiclePhotoController::class, 'destroy'])->name('vehicles.photos.destroy');

        Route::get('/produits/nouveau', [ProductController::class, 'create'])->name('products.create');
        Route::post('/produits', [ProductController::class, 'store'])->name('products.store');
        Route::get('/produits/{product}/modifier', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/produits/{product}', [ProductController::class, 'update'])->name('products.update');

        Route::post('/produits/{product}/photos', [ProductPhotoController::class, 'store'])->name('products.photos.store');
        Route::patch('/produits/{product}/photos/{photo}/principale', [ProductPhotoController::class, 'primary'])->name('products.photos.primary');
        Route::delete('/produits/{product}/photos/{photo}', [ProductPhotoController::class, 'destroy'])->name('products.photos.destroy');
    });

    Route::middleware('role:manager,accountant')->group(function () {
        Route::get('/paiements/nouveau', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/paiements', [PaymentController::class, 'store'])->name('payments.store');
    });

    Route::get('/biens/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/locataires/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('/produits/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/vehicules/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/chambres/{room}', [HotelRoomController::class, 'show'])->name('hotel-rooms.show');
    Route::get('/sejours/{stay}', [HotelStayController::class, 'show'])->name('hotel-stays.show');
});

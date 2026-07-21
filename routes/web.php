<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.browse');
Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.show');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
    ->middleware('guest')
    ->name('auth.google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->middleware('guest')
    ->name('auth.google.callback');

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $monthRevenue = Order::query()
            ->where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('grand_total');

        $newOrdersCount = Order::query()
            ->whereIn('status', ['pending', 'paid', 'processing'])
            ->whereIn('shipping_status', ['not_created', 'pending'])
            ->count();

        $totalProducts = Product::count();
        $lowStockCount = Product::where('stock', '>', 0)->where('stock', '<', 5)->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();
        $totalCustomers = Customer::count();

        $lowStockProducts = Product::query()
            ->where('stock', '<', 5)
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(5)
            ->get();

        $recentOrders = Order::query()
            ->with('customer')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'monthRevenue',
            'newOrdersCount',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalCustomers',
            'lowStockProducts',
            'recentOrders',
        ));
    })->name('dashboard');

    Route::resource('products', ProductController::class);
});

Route::post('/logout', function (Request $request) {
    auth()->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

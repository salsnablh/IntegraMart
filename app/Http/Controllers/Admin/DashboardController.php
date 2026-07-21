<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
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

        $trendStart = now()->subDays(29)->startOfDay();
        $trendEnd = now()->endOfDay();

        $trendRows = Order::query()
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as sales_date, SUM(COALESCE(total_amount, grand_total)) as revenue')
            ->groupBy('sales_date')
            ->orderBy('sales_date')
            ->get()
            ->keyBy('sales_date');

        $salesTrend = collect(CarbonPeriod::create($trendStart, $trendEnd))->map(function (Carbon $date) use ($trendRows) {
            $key = $date->format('Y-m-d');

            return [
                'label' => $date->format('d M'),
                'value' => (float) ($trendRows->get($key)->revenue ?? 0),
            ];
        });

        return view('dashboard', compact(
            'monthRevenue',
            'newOrdersCount',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalCustomers',
            'lowStockProducts',
            'recentOrders',
            'salesTrend',
        ));
    }
}

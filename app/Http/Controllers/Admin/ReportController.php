<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = isset($data['start_date'])
            ? Carbon::parse($data['start_date'])->startOfDay()
            : now()->startOfMonth();

        $endDate = isset($data['end_date'])
            ? Carbon::parse($data['end_date'])->endOfDay()
            : now()->endOfDay();

        $ordersQuery = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled');

        $totalRevenue = (clone $ordersQuery)->sum(DB::raw('COALESCE(total_amount, grand_total)'));
        $totalOrders = (clone $ordersQuery)->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $dailySales = (clone $ordersQuery)
            ->selectRaw('DATE(created_at) as sales_date, SUM(COALESCE(total_amount, grand_total)) as revenue, COUNT(*) as total_orders')
            ->groupBy('sales_date')
            ->orderBy('sales_date')
            ->get()
            ->keyBy('sales_date');

        $period = CarbonPeriod::create($startDate->copy()->startOfDay(), $endDate->copy()->startOfDay());
        $dailySummary = collect($period)->map(function (Carbon $date) use ($dailySales) {
            $key = $date->format('Y-m-d');
            $row = $dailySales->get($key);

            return [
                'date' => $key,
                'label' => $date->format('d M'),
                'revenue' => (float) ($row->revenue ?? 0),
                'orders' => (int) ($row->total_orders ?? 0),
            ];
        });

        $bestSellers = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('products.id, products.name, products.sku, SUM(order_items.qty) as sold_qty, SUM(order_items.subtotal) as revenue')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('sold_qty')
            ->limit(5)
            ->get();

        $transactions = (clone $ordersQuery)
            ->with(['customer', 'user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'dailySummary',
            'bestSellers',
            'transactions',
        ));
    }
}

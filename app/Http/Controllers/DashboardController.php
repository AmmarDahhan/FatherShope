<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $today = now()->startOfDay();
        $month = now()->startOfMonth();

        $dailySales = Sale::where('created_at', '>=', $today)->count();
        $dailyRevenue = Sale::where('created_at', '>=', $today)->sum('total');
        $monthlySales = Sale::where('created_at', '>=', $month)->count();
        $monthlyRevenue = Sale::where('created_at', '>=', $month)->sum('total');
        $totalProducts = Product::active()->count();
        $lowStockCount = Product::active()->lowStock()->count();

        return response()->json([
            'daily' => [
                'sales_count' => $dailySales,
                'revenue' => round($dailyRevenue, 2),
            ],
            'monthly' => [
                'sales_count' => $monthlySales,
                'revenue' => round($monthlyRevenue, 2),
            ],
            'products' => [
                'total' => $totalProducts,
                'low_stock' => $lowStockCount,
            ],
        ]);
    }

    public function inventoryAlerts(): JsonResponse
    {
        $lowStockProducts = Product::active()
            ->lowStock()
            ->select('id', 'name', 'stock_quantity', 'min_stock_alert', 'category')
            ->get();

        return response()->json([
            'products' => $lowStockProducts,
            'count' => $lowStockProducts->count(),
        ]);
    }
}

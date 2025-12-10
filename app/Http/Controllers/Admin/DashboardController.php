<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product; // Mengubah 'Produk' menjadi 'Product'
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Constants for cache configuration
    const CACHE_DURATION = 300; // 5 minutes
    const SHORT_CACHE_DURATION = 60; // 1 minute
    const SALES_CACHE_DURATION = 1800; // 30 minutes
    
    // Cache key constants
    const STATS_CACHE_KEY = 'dashboard_stats';
    const MONTHLY_SALES_CACHE_KEY = 'dashboard_monthly_sales';
    const RECENT_ORDERS_CACHE_KEY = 'dashboard_recent_orders';
    const SALES_SUMMARY_CACHE_KEY = 'dashboard_sales_summary';
    const LOW_STOCK_CACHE_KEY = 'dashboard_low_stock';

    /**
     * Display admin dashboard with statistics
     */
    public function index()
    {
        try {
            $data = $this->buildDashboardData();
            
            // Mengirim data ke tampilan Inertia.js
            return inertia('Admin/Dashboard', $data);
            
        } catch (\Exception $e) {
            Log::error('Error in DashboardController: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Mengembalikan data kosong jika terjadi error
            return inertia('Admin/Dashboard', $this->getEmptyDashboardData([
                'error' => 'Failed to load dashboard data'
            ]));
        }
    }

    /**
     * Build complete dashboard data
     */
    protected function buildDashboardData(): array
    {
        $stats = $this->getBasicStats();
        $recentOrders = $this->getRecentOrders();
        $monthlySales = $this->getMonthlySales();
        $salesSummary = $this->getSalesSummary();

        // Menggabungkan array untuk dikirim ke Inertia
        return [
            'stats' => array_merge($stats, $salesSummary), // Menggabungkan stats & sales summary
            'recent_orders' => $recentOrders,
            'monthly_sales' => array_values($monthlySales),
        ];
    }

    /**
     * Get basic statistics with optimized queries
     */
    protected function getBasicStats(): array
    {
        $cacheKey = self::STATS_CACHE_KEY;
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function() {
            try {
                // Menggabungkan query untuk Order dan OrderItem
                $orderStats = DB::table('orders')
                    ->selectRaw(
                        "COUNT(*) as total_orders, " .
                        "SUM(CASE WHEN status = 'Menunggu Pembayaran' THEN 1 ELSE 0 END) as pending_orders, " . // Menyesuaikan status
                        "SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as completed_orders"
                    )
                    ->first();
                
                // Menggabungkan query untuk Produk
                $productStats = DB::table('products') // Menggunakan tabel 'products'
                    ->selectRaw('COUNT(*) as total_products, COALESCE(SUM(stock), 0) as total_stock') // Menggunakan kolom 'stock'
                    ->first();

                // Mengambil total stock yang terjual dari order items
                $soldStock = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('orders.status', 'Selesai')
                    ->sum('quantity');

                $lowStockCount = DB::table('products')
                    ->where('stock', '<=', 5) // Aturan stok rendah (misalnya < 5)
                    ->count();

                $customerCount = User::where('role', 'user')->count();

                return [
                    'total_orders' => (int) ($orderStats->total_orders ?? 0),
                    'pending_orders' => (int) ($orderStats->pending_orders ?? 0),
                    'completed_orders' => (int) ($orderStats->completed_orders ?? 0),
                    'total_products' => (int) ($productStats->total_products ?? 0),
                    'total_customers' => (int) $customerCount,
                    'total_stock' => (int) ($productStats->total_stock ?? 0),
                    'sold_stock' => (int) ($soldStock ?? 0),
                    'low_stock_products' => (int) $lowStockCount,
                ];
            } catch (\Exception $e) {
                Log::error('Error getting basic stats: ' . $e->getMessage());
                return $this->getEmptyStats();
            }
        });
    }

    /**
     * Get recent orders with optimized relationships
     */
    protected function getRecentOrders(): array
    {
        $cacheKey = self::RECENT_ORDERS_CACHE_KEY;
        
        return Cache::remember($cacheKey, self::SHORT_CACHE_DURATION, function() {
            try {
                return Order::query()
                    ->with(['user:id,name,email'])
                    ->select('id', 'status', 'grand_total', 'user_id', 'created_at') // Menggunakan 'grand_total'
                    ->latest('created_at')
                    ->limit(10)
                    ->get()
                    ->map(function ($order) {
                        return [
                            'id' => $order->id,
                            'status' => $order->status,
                            'total' => $this->formatCurrency($order->grand_total),
                            'total_raw' => (float) $order->grand_total,
                            'created_at' => $order->created_at->format('d/m/Y H:i'),
                            'created_at_human' => $order->created_at->diffForHumans(),
                            'user' => $order->user ? [
                                'name' => $order->user->name,
                                'email' => $order->user->email,
                            ] : [
                                'name' => 'Guest User',
                                'email' => null,
                            ],
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Error getting recent orders: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get monthly sales data with better optimization
     */
    protected function getMonthlySales(): array
    {
        $currentYear = now()->year;
        $cacheKey = self::MONTHLY_SALES_CACHE_KEY . '_' . $currentYear;
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function() use ($currentYear) {
            try {
                $salesData = DB::table('orders')
                    ->selectRaw('
                        MONTH(created_at) as month,
                        COUNT(*) as total_orders,
                        COALESCE(SUM(grand_total), 0) as total_sales
                    ')
                    ->whereYear('created_at', $currentYear)
                    ->whereNotIn('status', ['Dibatalkan', 'Dikembalikan']) // Menyesuaikan status
                    ->groupBy(DB::raw('MONTH(created_at)'))
                    ->orderBy('month')
                    ->get()
                    ->keyBy('month');

                $monthlyData = [];
                for ($month = 1; $month <= 12; $month++) {
                    $actualData = $salesData->get($month);
                    
                    $monthlyData[$month] = [
                        'month' => $month,
                        'month_name' => Carbon::create(null, $month, 1)->isoFormat('MMMM'), // Menggunakan isoFormat untuk nama bulan
                        'month_short' => Carbon::create(null, $month, 1)->isoFormat('MMM'),
                        'total_orders' => $actualData ? (int) $actualData->total_orders : 0,
                        'total_sales' => $actualData ? (float) $actualData->total_sales : 0,
                        'total_sales_formatted' => $actualData ? 
                            $this->formatCurrency($actualData->total_sales) : 
                            $this->formatCurrency(0),
                    ];
                }

                return $monthlyData;
            } catch (\Exception $e) {
                Log::error('Error getting monthly sales: ' . $e->getMessage());
                return $this->getEmptyMonthlySales();
            }
        });
    }

    /**
     * Get comprehensive sales summary
     */
    protected function getSalesSummary(): array
    {
        $cacheKey = self::SALES_SUMMARY_CACHE_KEY;
        
        return Cache::remember($cacheKey, self::SALES_CACHE_DURATION, function() {
            try {
                $result = DB::table('orders')
                    ->selectRaw('
                        COALESCE(SUM(CASE WHEN DATE(created_at) = CURDATE() THEN grand_total ELSE 0 END), 0) as today_sales,
                        COALESCE(SUM(CASE WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) THEN grand_total ELSE 0 END), 0) as week_sales,
                        COALESCE(SUM(CASE WHEN YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE()) THEN grand_total ELSE 0 END), 0) as month_sales,
                        COALESCE(SUM(CASE WHEN YEAR(created_at) = YEAR(CURDATE()) THEN grand_total ELSE 0 END), 0) as year_sales
                    ')
                    ->whereNotIn('status', ['Dibatalkan', 'Dikembalikan'])
                    ->first();

                return [
                    'today_sales' => (float) ($result->today_sales ?? 0),
                    'week_sales' => (float) ($result->week_sales ?? 0),
                    'month_sales' => (float) ($result->month_sales ?? 0),
                    'year_sales' => (float) ($result->year_sales ?? 0),
                    'today_sales_formatted' => $this->formatCurrency($result->today_sales ?? 0),
                    'week_sales_formatted' => $this->formatCurrency($result->week_sales ?? 0),
                    'month_sales_formatted' => $this->formatCurrency($result->month_sales ?? 0),
                    'year_sales_formatted' => $this->formatCurrency($result->year_sales ?? 0),
                ];
            } catch (\Exception $e) {
                Log::error('Error getting sales summary: ' . $e->getMessage());
                return $this->getEmptySalesSummary();
            }
        });
    }

    /**
     * Generate a unique key for the cache, for dynamic data based on time.
     */
    protected function generateCacheKey(string $baseKey, string $timeFormat = 'Y-m-d-H-i'): string
    {
        // For sales summary, cache is daily, so only date is needed
        if ($baseKey === self::SALES_SUMMARY_CACHE_KEY) {
            return $baseKey . '_' . now()->format('Y-m-d');
        }
        // For other, more frequent data, hourly or minutely keys
        return $baseKey . '_' . now()->format($timeFormat);
    }
    
    // ... metode helper lainnya
    protected function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    protected function getEmptyDashboardData(array $additional = []): array
    {
        return array_merge([
            'stats' => $this->getEmptyStats(),
            'recent_orders' => [],
            'monthly_sales' => array_values($this->getEmptyMonthlySales()),
            'sales_summary' => $this->getEmptySalesSummary()
        ], $additional);
    }

    protected function getEmptyStats(): array
    {
        return [
            'total_orders' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0,
            'total_products' => 0,
            'total_customers' => 0,
            'total_stock' => 0,
            'sold_stock' => 0,
            'low_stock_products' => 0,
        ];
    }
    protected function getEmptyMonthlySales(): array
    {
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[$month] = [
                'month' => $month,
                'month_name' => Carbon::create(null, $month, 1)->isoFormat('MMMM'),
                'month_short' => Carbon::create(null, $month, 1)->isoFormat('MMM'),
                'total_orders' => 0,
                'total_sales' => 0,
                'total_sales_formatted' => $this->formatCurrency(0),
            ];
        }
        return $monthlyData;
    }
    protected function getEmptySalesSummary(): array
    {
        return [
            'today_sales' => 0,
            'week_sales' => 0,
            'month_sales' => 0,
            'year_sales' => 0,
            'today_sales_formatted' => $this->formatCurrency(0),
            'week_sales_formatted' => $this->formatCurrency(0),
            'month_sales_formatted' => $this->formatCurrency(0),
            'year_sales_formatted' => $this->formatCurrency(0),
        ];
    }
}
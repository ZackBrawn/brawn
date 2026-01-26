<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderStatusUpdated; // Pastikan notifikasi sudah dibuat
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        
        // Define all possible order statuses
        $statusOptions = [
            'all' => 'Semua Status',
            'Menunggu Pembayaran' => 'Menunggu Pembayaran',
            'Dibayar' => 'Dibayar',
            'Diproses' => 'Diproses',
            'Dikirim' => 'Dikirim',
            'Selesai' => 'Selesai',
            'Dibatalkan' => 'Dibatalkan',
        ];

        $query = Order::query()
            ->with(['user' => fn($q) => $q->select('id', 'name', 'email', 'phone_number')])
            ->select('id', 'user_id', 'grand_total', 'status', 'created_at', 'updated_at');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    });
            });
        }

        // Apply status filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(10);
        
        // Get status counts in a single efficient query
        $statusCounts = DB::table('orders')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Add 'all' status count
        $statusCounts['all'] = array_sum($statusCounts);
        
        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $filters,
            'statusCounts' => $statusCounts,
            'statusOptions' => $statusOptions,
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Eager load relationships
        $order->load([
            'user' => fn($q) => $q->select('id', 'name', 'email', 'phone_number'),
            'address' => fn($q) => $q->select('id', 'recipient_name', 'label', 'full_address', 'city', 'province', 'postal_code'),
            'paymentMethod' => fn($q) => $q->select('id', 'name', 'account_name', 'account_number'),
            'orderItems.product' => fn($q) => $q->select('id', 'name', 'image_url', 'price')
        ]);
        
        // Define all possible order statuses
        $statusOptions = [
            'Menunggu Pembayaran' => 'Menunggu Pembayaran',
            'Dibayar' => 'Dibayar',
            'Diproses' => 'Diproses',
            'Dikirim' => 'Dikirim',
            // 'Selesai' => 'Selesai', // Usually automated or manual
            'Selesai' => 'Selesai',
            'Dibatalkan' => 'Dibatalkan',
        ];

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'statusOptions' => $statusOptions,
        ]);
    }
    
    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([
                'Menunggu Pembayaran', 'Dibayar', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'
            ])],
        ]);

        try {
            DB::beginTransaction();

            // Capture previous status before update
            $previousStatus = $order->status;

            $order->status = $validated['status'];
            $order->save();

            // Create a status history record
            $order->orderStatusHistories()->create([
                'status' => $validated['status'],
                'notes' => $request->input('notes') // Optional notes for status change
            ]);

            // Create notification for the user
            $user = $order->user;
            if ($user) {
                $user->notify(new OrderStatusUpdated($order, $previousStatus));
            }
            
            DB::commit();

            return redirect()->back()->with('success', "Status pesanan #{$order->id} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Order status update failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui status pesanan. Silakan coba lagi.']);
        }
    }
}
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $wishlist = [];
        
        if ($user) {
            \Log::info('Sharing authenticated user', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'session_id' => $request->session()->getId(),
                'session_data' => $request->session()->all()
            ]);
            $wishlist = $user->wishlist()
                ->with('product')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ];
                });
        } else {
            \Log::info('No authenticated user', [
                'session_id' => $request->session()->getId(),
                'session_data' => $request->session()->all()
            ]);
        }

        $sharedData = [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ] : null
            ],
            'wishlist' => $wishlist,
            'notifications' => $user ? $user->notifications()->latest()->take(5)->get() : [],
            'unread_notifications_count' => $user ? $user->unreadNotifications()->count() : 0,
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'cart' => array_values($request->session()->get('cart', [])),
            'session' => [
                'id' => $request->session()->getId(),
            ],
        ];

        // Debug log the shared data
        \Log::info('Shared Inertia data', [
            'auth_user' => $user ? $user->id : 'null',
            'session_id' => $request->session()->getId(),
            'shared_data' => $sharedData
        ]);

        return array_merge(parent::share($request), $sharedData);
    }
}

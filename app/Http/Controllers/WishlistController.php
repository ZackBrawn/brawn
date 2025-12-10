<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Menampilkan daftar wishlist pengguna.
     */
    public function index()
    {
        $user = auth()->user();
        $wishlist = $user->wishlist()->with('product.category')->get();
        $products = $wishlist->map(function($item) {
            return $item->product;
        });

        return Inertia::render('User/Wishlist/Index', [
            'wishlist' => $wishlist,
            'products' => [
                'data' => $products,
                'links' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 15,
                    'total' => $products->count()
                ]
            ],
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]
        ]);
    }

    /**
     * Menambahkan produk ke wishlist.
     */
    public function add(Product $product)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus login terlebih dahulu.',
                    'redirect' => route('login')
                ], 401);
            }

            $user = auth()->user();
            
            \Log::info('Adding product to wishlist', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product_slug' => $product->slug,
                'wishlist_count' => $user->wishlist()->count()
            ]);

            // Check if product is already in wishlist
            $existingWishlist = $user->wishlist()
                ->where('product_id', $product->id)
                ->first();
            
            if ($existingWishlist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk sudah ada di wishlist.',
                    'in_wishlist' => true
                ], 200);
            }

            // Add to wishlist
            $wishlistItem = $user->wishlist()->create([
                'product_id' => $product->id
            ]);

            if (!$wishlistItem) {
                throw new \Exception('Gagal menambahkan produk ke wishlist');
            }

            // Get updated wishlist with fresh data
            $wishlist = $user->wishlist()
                ->with('product')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'product' => $item->product ? [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'slug' => $item->product->slug,
                            'price' => $item->product->price,
                            'image_url' => $item->product->image_url,
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke wishlist.',
                'wishlist' => $wishlist,
                'in_wishlist' => true
            ]);
            
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $errorTrace = $e->getTraceAsString();
            
            \Log::error('Error in WishlistController@add', [
                'message' => $errorMessage,
                'trace' => $errorTrace,
                'product_id' => $product->id ?? null,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan ke wishlist: ' . $errorMessage,
                'error' => config('app.debug') ? $errorMessage : 'Terjadi kesalahan',
                'trace' => config('app.debug') ? $errorTrace : null
            ], 500);
        }
    }

    /**
     * Menghapus produk dari wishlist.
     */
    public function remove($productId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus login terlebih dahulu.',
                    'redirect' => route('login')
                ], 401);
            }

            $user = auth()->user();
            
            // Log the raw input for debugging
            \Log::info('Raw productId input:', ['productId' => $productId]);

            // Handle different input types for productId
            $productId = is_array($productId) ? ($productId['product_id'] ?? $productId['id'] ?? null) : $productId;
            
            // If it's an object, try to get the id
            if (is_object($productId) && method_exists($productId, 'getKey')) {
                $productId = $productId->getKey();
            }
            
            // If we still don't have a valid ID, log and return error
            if (!$productId) {
                throw new \Exception('Invalid product ID provided');
            }

            \Log::info('Attempting to remove product from wishlist', [
                'user_id' => $user->id,
                'product_id' => $productId,
                'wishlist_count_before' => $user->wishlist()->count(),
                'type' => is_numeric($productId) ? 'numeric_id' : 'slug'
            ]);

            // Find the product by ID
            $product = Product::find($productId);

            if (!$product) {
                $product = Product::where('slug', $productId)->first();
            }

            if (!$product) {
                \Log::error('Product not found for removal', [
                    'product_id' => $productId,
                    'all_products' => Product::all()->pluck('id', 'slug')
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.'
                ], 404);
            }

            $user = auth()->user();
            \Log::info('Removing from wishlist', [
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);
            
            $deleted = $user->wishlist()->where('product_id', $product->id)->delete();
            
            \Log::info('Wishlist removal result', [
                'deleted' => $deleted,
                'product_id' => $product->id
            ]);

            if ($deleted) {
                // Get updated wishlist with fresh data
                $wishlist = $user->wishlist()
                    ->with('product')
                    ->get()
                    ->map(function($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'created_at' => $item->created_at,
                            'updated_at' => $item->updated_at,
                            'product' => $item->product ? [
                                'id' => $item->product->id,
                                'name' => $item->product->name,
                                'slug' => $item->product->slug,
                                'price' => $item->product->price,
                                'image_url' => $item->product->image_url,
                            ] : null
                        ];
                    });
                
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus dari wishlist.',
                    'wishlist' => $wishlist,
                    'in_wishlist' => false
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan di wishlist.',
                    'in_wishlist' => false
                ], 200);
            }
            
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $errorTrace = $e->getTraceAsString();
            
            \Log::error('Error in WishlistController@remove', [
                'message' => $errorMessage,
                'trace' => $errorTrace,
                'product_id' => $productId ?? null,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus dari wishlist: ' . $errorMessage,
                'error' => config('app.debug') ? $errorMessage : 'Terjadi kesalahan',
                'trace' => config('app.debug') ? $errorTrace : null,
                'in_wishlist' => true // Assume still in wishlist on error
            ], 500);
        }
    }
}
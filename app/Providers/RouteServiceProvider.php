<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicit route model binding for Product using ID or slug
        Route::bind('product', function ($value) {
            // First try to find by ID
            $product = \App\Models\Product::find($value);
            
            // If not found by ID, try by slug
            if (!$product) {
                $product = \App\Models\Product::where('slug', $value)->first();
            }
            
            if (!$product) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                    'No query results for model [App\\Models\\Product]',
                    $value
                );
            }
            
            return $product;
        });
    }
}

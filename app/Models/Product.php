<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image_url',
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Get the URL for the product image.
     *
     * @return string
     */
    public function getImageUrlAttribute($value)
    {
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        return $value ? asset('storage/' . $value) : null;
    }
}
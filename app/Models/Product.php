<?php

namespace App\Models;

use App\Models\User;
use App\Models\Brand;
use App\Models\PurchaseProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'brand_id',
        'category_id',
        'thumnail_img',
        'gallary_img',
        'stock',
        'status',
        'purchase_price',
        'sale_price',
        'feature',
        'barcode',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function purchaseProducts()
    {
        return $this->hasMany(PurchaseProduct::class, 'product_id');
    }


}

<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'user_id',
    ];

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }
    public function category_translations(){
    	return $this->hasMany(CategoryTranslation::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            foreach ($category->brands as $brand) {
                // Delete brand translations
                foreach ($brand->brand_translations as $translation) {
                    $translation->delete();
                }

                // Delete products and purchaseProducts
                foreach ($brand->products as $product) {
                    $product->purchaseProducts()->delete();
                    $product->delete();
                }

                // Delete brand image if needed (optional)
                if ($brand->image && \Storage::disk('public')->exists($brand->image)) {
                    \Storage::disk('public')->delete($brand->image);
                }

                $brand->delete();
            }

            // Delete category translations
            foreach ($category->category_translations as $translation) {
                $translation->delete();
            }
        });
    }

}

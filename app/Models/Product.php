<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'category',
        'description',
        'price',
        'stock',
        'is_active',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (! $product->slug || $product->isDirty('name')) {
                $baseSlug = Str::slug($product->name);
                $slug = $baseSlug;
                $counter = 2;

                while (static::where('slug', $slug)->whereKeyNot($product->id)->exists()) {
                    $slug = $baseSlug.'-'.$counter;
                    $counter++;
                }

                $product->slug = $slug;
            }
        });
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function objectFiles(): MorphMany
    {
        return $this->morphMany(ObjectFile::class, 'related');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk(config('filesystems.product_images_disk', 'public'))->url($this->image_path);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

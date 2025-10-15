<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    protected $appends = ['image_url', 'large_image_url'];

    protected $casts = [
        'price' => 'integer',
        'cost' => 'integer',
        'stock' => 'integer',
        'quantity' => 'integer',
        'units' => 'integer',
    ];

    const ACTIVE = '0';

    const INACTIVE = '1';

    const TOP_INACTIVE = '1';

    const TOP_ACTIVE = '0';

    const HAS_PRESENTATION_INACTIVE = '1';

    const HAS_PRESENTATION_ACTIVE = '0';

    protected $attributes = [
        'top' => '1',
        'has_presentations' => '1',
        'quantity' => 0,
        'units' => 0,
        'status' => '0',
    ];

    public function presentations()
    {
        return $this->hasMany(Presentation::class);
    }

    public function getStockUnitsLabelAttribute()
    {
        if (intval($this->has_inventory)) {
            return 'No aplica';
        }

        if (intval($this->has_presentations)) {
            return $this->stock;
        }

        $units = $this->units - ($this->stock * $this->quantity);

        return $this->stock.'-'.$units;
    }

    public function getStockUnitsAttribute()
    {

        if (intval($this->has_presentations)) {
            return $this->stock;
        }

        return $this->units - ($this->stock * $this->quantity);

    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function taxRates(): BelongsToMany
    {
        return $this->belongsToMany(TaxRate::class)->withPivot('value');
    }

    public function scopeFilterBarcode($query, $filter, $search)
    {
        if ($filter === 'reference') {
            $query->orWhere('barcode', $search);
        }
    }

    /**
     * Get the image URL from Cloudinary
     */
    public function getImageUrlAttribute()
    {
        if (empty($this->cloudinary_public_id)) {
            return asset('images/no-product-image.svg');
        }

        // URLs directas para productos específicos
        if ($this->cloudinary_public_id === 'medicine_5378888_byfqr9') {
            return 'https://res.cloudinary.com/dxktixdby/image/upload/w_150,h_150,c_fill/v1755273090/' . $this->cloudinary_public_id . '.png';
        }
        
        if ($this->cloudinary_public_id === 'syringe_5430356_d9h5db') {
            return 'https://res.cloudinary.com/dxktixdby/image/upload/w_150,h_150,c_fill/v1755276131/' . $this->cloudinary_public_id . '.png';
        }

        try {
            $imageService = app(\App\Services\Contracts\ImageServiceInterface::class);
            return $imageService->getProductThumbnailUrl($this->id, 150);
        } catch (\Exception $e) {
            return asset('images/no-product-image.svg');
        }
    }

    /**
     * Get large image URL for details
     */
    public function getLargeImageUrlAttribute()
    {
        if (empty($this->cloudinary_public_id)) {
            return asset('images/no-product-image.svg');
        }

        try {
            $imageService = app(\App\Services\Contracts\ImageServiceInterface::class);
            return $imageService->getProductImageUrl($this->id, [
                'width' => 400,
                'height' => 400,
                'crop' => 'fit'
            ]);
        } catch (\Exception $e) {
            return asset('images/no-product-image.png');
        }
    }
}

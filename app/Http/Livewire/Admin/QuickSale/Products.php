<?php

namespace App\Http\Livewire\Admin\QuickSale;

use App\Models\Category;
use App\Models\Presentation;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Products extends Component
{
    protected $listeners = ['render' => 'refreshProducts'];

    public $products, $presentations, $categories;

    public $openModal = false;

    public function mount()
    {
        $this->refreshProducts();
    }

    public function render()
    {
        return view('livewire.admin.quick-sale.products');
    }

    public function refreshProducts()
    {
        // Cachear categorías por 10 minutos
        $this->categories = \Cache::remember('categories_list_' . tenant('id'), 600, function() {
            return Category::orderBy('name', 'ASC')
                ->get()
                ->pluck('name', 'id');
        });

        // Cachear productos por 2 minutos (se actualiza stock frecuentemente)
        $this->products = \Cache::remember('products_list_' . tenant('id'), 120, function() {
            return Product::select(['id', 'reference', 'category_id', 'name', 'price', 'cloudinary_public_id', DB::raw('CASE WHEN stock > 0 || units > 0 || has_inventory = "1" THEN 1 ELSE 0 END AS has_stock')])
                ->where('status', '0')
                ->orderBy('top', 'ASC')
                ->orderBy('name', 'ASC')
                ->get()
                ->map(function($product) {
                    $productArray = $product->toArray();
                    $productArray['image_url'] = $product->image_url; // Agregar la URL de imagen
                    return $productArray;
                })
                ->toArray();
        });

        // Cachear presentaciones por 5 minutos
        $this->presentations = \Cache::remember('presentations_list_' . tenant('id'), 300, function() {
            return Presentation::select('id', 'name', 'price', 'product_id')
                ->where('status', '0')
                ->get()
                ->toArray();
        });
    }
}

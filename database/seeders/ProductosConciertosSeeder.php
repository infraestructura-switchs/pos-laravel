<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class ProductosConciertosSeeder extends Seeder
{
    public function run()
    {
        // Crear categorías específicas para concierto
        $categories = [
            'Bebidas Alcohólicas',
            'Bebidas No Alcohólicas', 
            'Comida Rápida',
            'Snacks y Dulces',
            'Cigarrillos y Tabaco',
            'Merchandising',
            'Combos'
        ];

        $categoryIds = [];
        foreach ($categories as $categoryName) {
            $category = Category::firstOrCreate(['name' => $categoryName]);
            $categoryIds[$categoryName] = $category->id;
        }

        // Productos para concierto con estructura del sistema
        $productos = [
            // BEBIDAS ALCOHÓLICAS
            [
                'barcode' => 7702355013014,
                'reference' => 'BEB-001',
                'category_id' => $categoryIds['Bebidas Alcohólicas'],
                'name' => 'Cerveza Poker Lata',
                'cost' => 2500,
                'price' => 4500,
                'stock' => 200,
                'quantity' => 200,
                'units' => 200,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702355020013,
                'reference' => 'BEB-002', 
                'category_id' => $categoryIds['Bebidas Alcohólicas'],
                'name' => 'Cerveza Club Colombia',
                'cost' => 3000,
                'price' => 5000,
                'stock' => 150,
                'quantity' => 150,
                'units' => 150,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702012345018,
                'reference' => 'BEB-003',
                'category_id' => $categoryIds['Bebidas Alcohólicas'],
                'name' => 'Ron Medellín Añejo',
                'cost' => 55000,
                'price' => 85000,
                'stock' => 50,
                'quantity' => 50,
                'units' => 50,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702012345025,
                'reference' => 'BEB-004',
                'category_id' => $categoryIds['Bebidas Alcohólicas'],
                'name' => 'Aguardiente Antioqueño',
                'cost' => 28000,
                'price' => 45000,
                'stock' => 80,
                'quantity' => 80,
                'units' => 80,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702012345032,
                'reference' => 'BEB-005',
                'category_id' => $categoryIds['Bebidas Alcohólicas'],
                'name' => 'Whisky Old Parr',
                'cost' => 75000,
                'price' => 120000,
                'stock' => 30,
                'quantity' => 30,
                'units' => 30,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702012345049,
                'reference' => 'BEB-006',
                'category_id' => $categoryIds['Bebidas Alcohólicas'],
                'name' => 'Vodka Smirnoff',
                'cost' => 40000,
                'price' => 65000,
                'stock' => 40,
                'quantity' => 40,
                'units' => 40,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702012345056,
                'reference' => 'BEB-007',
                'category_id' => $categoryIds['Bebidas Alcohólicas'],
                'name' => 'Cerveza Artesanal IPA',
                'cost' => 7000,
                'price' => 12000,
                'stock' => 60,
                'quantity' => 60,
                'units' => 60,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702012345063,
                'reference' => 'BEB-008',
                'category_id' => $categoryIds['Bebidas Alcohólicas'],
                'name' => 'Vino Tinto Copa',
                'cost' => 9000,
                'price' => 15000,
                'stock' => 40,
                'quantity' => 40,
                'units' => 40,
                'has_presentations' => '1',
            ],

            // BEBIDAS NO ALCOHÓLICAS
            [
                'barcode' => 7702000012012,
                'reference' => 'BEB-NA-001',
                'category_id' => $categoryIds['Bebidas No Alcohólicas'],
                'name' => 'Agua Cristal',
                'cost' => 1200,
                'price' => 2000,
                'stock' => 300,
                'quantity' => 300,
                'units' => 300,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702000012029,
                'reference' => 'BEB-NA-002',
                'category_id' => $categoryIds['Bebidas No Alcohólicas'],
                'name' => 'Coca Cola',
                'cost' => 2000,
                'price' => 3500,
                'stock' => 250,
                'quantity' => 250,
                'units' => 250,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702000012036,
                'reference' => 'BEB-NA-003',
                'category_id' => $categoryIds['Bebidas No Alcohólicas'],
                'name' => 'Pepsi',
                'cost' => 1800,
                'price' => 3200,
                'stock' => 200,
                'quantity' => 200,
                'units' => 200,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 9002490100026,
                'reference' => 'BEB-NA-004',
                'category_id' => $categoryIds['Bebidas No Alcohólicas'],
                'name' => 'Red Bull',
                'cost' => 5000,
                'price' => 8000,
                'stock' => 100,
                'quantity' => 100,
                'units' => 100,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702000012043,
                'reference' => 'BEB-NA-005',
                'category_id' => $categoryIds['Bebidas No Alcohólicas'],
                'name' => 'Jugo de Naranja Natural',
                'cost' => 2500,
                'price' => 5000,
                'stock' => 80,
                'quantity' => 80,
                'units' => 80,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702000012050,
                'reference' => 'BEB-NA-006',
                'category_id' => $categoryIds['Bebidas No Alcohólicas'],
                'name' => 'Café Americano',
                'cost' => 2000,
                'price' => 4000,
                'stock' => 100,
                'quantity' => 100,
                'units' => 100,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702000012067,
                'reference' => 'BEB-NA-007',
                'category_id' => $categoryIds['Bebidas No Alcohólicas'],
                'name' => 'Té Helado',
                'cost' => 2500,
                'price' => 4500,
                'stock' => 120,
                'quantity' => 120,
                'units' => 120,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7702000012074,
                'reference' => 'BEB-NA-008',
                'category_id' => $categoryIds['Bebidas No Alcohólicas'],
                'name' => 'Botella de Agua 1L',
                'cost' => 2000,
                'price' => 3500,
                'stock' => 150,
                'quantity' => 150,
                'units' => 150,
                'has_presentations' => '1',
            ],

            // COMIDA RÁPIDA
            [
                'barcode' => 7701000001018,
                'reference' => 'FOOD-001',
                'category_id' => $categoryIds['Comida Rápida'],
                'name' => 'Hamburguesa Clásica',
                'cost' => 12000,
                'price' => 18000,
                'stock' => 100,
                'quantity' => 100,
                'units' => 100,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7701000001025,
                'reference' => 'FOOD-002',
                'category_id' => $categoryIds['Comida Rápida'],
                'name' => 'Hamburguesa Doble Carne',
                'cost' => 16000,
                'price' => 25000,
                    'stock' => 80,
                    'quantity' => 80,
                'units' => 80,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7701000001032,
                'reference' => 'FOOD-003',
                'category_id' => $categoryIds['Comida Rápida'],
                'name' => 'Hot Dog Especial',
                'cost' => 7000,
                'price' => 12000,
                'stock' => 120,
                'quantity' => 120,
                'units' => 120,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7701000001049,
                'reference' => 'FOOD-004',
                'category_id' => $categoryIds['Comida Rápida'],
                'name' => 'Papas Fritas Grandes',
                'cost' => 4000,
                'price' => 8000,
                'stock' => 150,
                'quantity' => 150,
                'units' => 150,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7701000001056,
                'reference' => 'FOOD-005',
                'category_id' => $categoryIds['Comida Rápida'],
                'name' => 'Nachos con Queso',
                'cost' => 9000,
                'price' => 15000,
                'stock' => 90,
                'quantity' => 90,
                'units' => 90,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7701000001063,
                'reference' => 'FOOD-006',
                'category_id' => $categoryIds['Comida Rápida'],
                'name' => 'Empanada de Carne',
                'cost' => 2500,
                'price' => 4500,
                'stock' => 200,
                'quantity' => 200,
                'units' => 200,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7701000001070,
                'reference' => 'FOOD-007',
                'category_id' => $categoryIds['Comida Rápida'],
                'name' => 'Arepa con Queso',
                'cost' => 3500,
                'price' => 6000,
                'stock' => 100,
                'quantity' => 100,
                'units' => 100,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7701000001087,
                'reference' => 'FOOD-008',
                'category_id' => $categoryIds['Comida Rápida'],
                'name' => 'Sandwich Cubano',
                'cost' => 10000,
                'price' => 16000,
                'stock' => 60,
                'quantity' => 60,
                'units' => 60,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7701000001094,
                'reference' => 'FOOD-009',
                'category_id' => $categoryIds['Comida Rápida'],
                'name' => 'Salchipapa',
                'cost' => 8500,
                'price' => 14000,
                'stock' => 90,
                'quantity' => 90,
                'units' => 90,
                'has_presentations' => '1',
            ],

            // SNACKS Y DULCES
            [
                'barcode' => 7703000001015,
                'reference' => 'SNACK-001',
                'category_id' => $categoryIds['Snacks y Dulces'],
                'name' => 'Maní Salado',
                'cost' => 1800,
                'price' => 3000,
                'stock' => 150,
                'quantity' => 150,
                'units' => 150,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7703000001022,
                'reference' => 'SNACK-002',
                'category_id' => $categoryIds['Snacks y Dulces'],
                'name' => 'Papas Margarita',
                'cost' => 1500,
                'price' => 2500,
                'stock' => 200,
                'quantity' => 200,
                'units' => 200,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7703000001039,
                'reference' => 'SNACK-003',
                'category_id' => $categoryIds['Snacks y Dulces'],
                'name' => 'Palomitas de Maíz',
                'cost' => 2000,
                'price' => 4000,
                'stock' => 120,
                'quantity' => 120,
                'units' => 120,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7703000001046,
                'reference' => 'SNACK-004',
                'category_id' => $categoryIds['Snacks y Dulces'],
                'name' => 'Chocolate Jet',
                'cost' => 2000,
                'price' => 3500,
                'stock' => 100,
                'quantity' => 100,
                'units' => 100,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7703000001053,
                'reference' => 'SNACK-005',
                'category_id' => $categoryIds['Snacks y Dulces'],
                'name' => 'Chicles Trident',
                'cost' => 1200,
                'price' => 2000,
                'stock' => 80,
                'quantity' => 80,
                'units' => 80,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 4001810068103,
                'reference' => 'SNACK-006',
                'category_id' => $categoryIds['Snacks y Dulces'],
                'name' => 'Gomitas Haribo',
                'cost' => 2500,
                'price' => 4500,
                'stock' => 90,
                'quantity' => 90,
                'units' => 90,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7703000001067,
                'reference' => 'SNACK-007',
                'category_id' => $categoryIds['Snacks y Dulces'],
                'name' => 'Mix de Frutos Secos',
                'cost' => 3500,
                'price' => 6000,
                'stock' => 60,
                'quantity' => 60,
                'units' => 60,
                'has_presentations' => '1',
            ],

            // CIGARRILLOS Y TABACO
            [
                'barcode' => 7704000001012,
                'reference' => 'CIG-001',
                'category_id' => $categoryIds['Cigarrillos y Tabaco'],
                'name' => 'Marlboro Rojo',
                'cost' => 6000,
                'price' => 8500,
                'stock' => 50,
                'quantity' => 50,
                'units' => 50,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7704000001029,
                'reference' => 'CIG-002',
                'category_id' => $categoryIds['Cigarrillos y Tabaco'],
                'name' => 'Lucky Strike',
                'cost' => 6500,
                'price' => 9000,
                'stock' => 40,
                'quantity' => 40,
                'units' => 40,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 3086123456789,
                'reference' => 'CIG-003',
                'category_id' => $categoryIds['Cigarrillos y Tabaco'],
                'name' => 'Encendedor BIC',
                'cost' => 1800,
                'price' => 3000,
                'stock' => 100,
                'quantity' => 100,
                'units' => 100,
                'has_presentations' => '1',
            ],

            // MERCHANDISING
            [
                'barcode' => 7705000001019,
                'reference' => 'MERCH-001',
                'category_id' => $categoryIds['Merchandising'],
                'name' => 'Camiseta del Concierto',
                'cost' => 20000,
                'price' => 35000,
                'stock' => 100,
                'quantity' => 100,
                'units' => 100,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7705000001026,
                'reference' => 'MERCH-002',
                'category_id' => $categoryIds['Merchandising'],
                'name' => 'Gorra Oficial',
                'cost' => 15000,
                'price' => 25000,
                'stock' => 80,
                'quantity' => 80,
                'units' => 80,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7705000001033,
                'reference' => 'MERCH-003',
                'category_id' => $categoryIds['Merchandising'],
                'name' => 'Pulsera del Evento',
                'cost' => 4000,
                'price' => 8000,
                'stock' => 200,
                'quantity' => 200,
                'units' => 200,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7705000001040,
                'reference' => 'MERCH-004',
                'category_id' => $categoryIds['Merchandising'],
                'name' => 'Poster del Artista',
                'cost' => 8000,
                'price' => 15000,
                'stock' => 50,
                'quantity' => 50,
                'units' => 50,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7705000001057,
                'reference' => 'MERCH-005',
                'category_id' => $categoryIds['Merchandising'],
                'name' => 'Pin Coleccionable',
                'cost' => 2500,
                'price' => 5000,
                'stock' => 150,
                'quantity' => 150,
                'units' => 150,
                'has_presentations' => '1',
            ],

            // COMBOS
            [
                'barcode' => 7706000001016,
                'reference' => 'COMBO-001',
                'category_id' => $categoryIds['Combos'],
                'name' => 'Combo Papas + Gaseosa',
                'cost' => 6000,
                'price' => 10000,
                'stock' => 80,
                'quantity' => 80,
                'units' => 80,
                'has_presentations' => '1',
            ],
            [
                'barcode' => 7706000001023,
                'reference' => 'COMBO-002',
                'category_id' => $categoryIds['Combos'],
                'name' => 'Combo Hamburguesa Completo',
                'cost' => 18000,
                'price' => 28000,
                'stock' => 70,
                'quantity' => 70,
                'units' => 70,
                'has_presentations' => '1',
            ]
        ];

        // Crear productos y asignar tax rates
        foreach ($productos as $productoData) {
            $product = Product::create($productoData);
            
            // Asignar tax rate apropiado (3 = IVA común para productos)
            $taxRateId = 3; // IVA común
            
            // Productos alcohólicos y cigarrillos pueden tener impuestos especiales
            if (str_contains($product->name, 'Cerveza') || 
                str_contains($product->name, 'Ron') || 
                str_contains($product->name, 'Whisky') || 
                str_contains($product->name, 'Vodka') ||
                str_contains($product->name, 'Aguardiente') ||
                str_contains($product->name, 'Vino') ||
                str_contains($product->name, 'Marlboro') ||
                str_contains($product->name, 'Lucky Strike')) {
                
                // Si existe tax rate para productos con impuestos especiales
                $specialTaxRate = TaxRate::where('name', 'LIKE', '%INC%')->first();
                if ($specialTaxRate) {
                    $product->taxRates()->attach($specialTaxRate->id, ['value' => 300]);
                } else {
                    $product->taxRates()->attach($taxRateId);
                }
            } else {
                $product->taxRates()->attach($taxRateId);
            }

            // Crear presentación por defecto
            $product->presentations()->create([
                'name' => 'Unidad',
                'quantity' => 1,
                'price' => $product->price,
            ]);
        }
    }
}
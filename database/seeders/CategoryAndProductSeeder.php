<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class CategoryAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $categories = [
            [
                'category_name' => 'Traditional Clothing',
                'description' => 'Authentic Karen traditional clothing including dresses, shirts, and accessories.',
            ],
            [
                'category_name' => 'Handwoven Textiles',
                'description' => 'Beautiful handwoven textiles made using traditional Karen weaving techniques.',
            ],
            [
                'category_name' => 'Jewelry',
                'description' => 'Traditional Karen jewelry including necklaces, bracelets, and earrings.',
            ],
            [
                'category_name' => 'Home Decor',
                'description' => 'Decorative items for your home featuring Karen cultural designs.',
            ],
            [
                'category_name' => 'Bags & Accessories',
                'description' => 'Handmade bags and accessories with traditional Karen patterns.',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'category_name' => $categoryData['category_name'],
                'description' => $categoryData['description'],
            ]);
        }

        // Create Products
        $products = [
            // Traditional Clothing
            [
                'category_name' => 'Traditional Clothing',
                'products' => [
                    [
                        'title' => 'Karen Traditional Dress',
                        'description' => 'Beautiful handwoven traditional Karen dress with intricate patterns.',
                        'price' => 129.99,
                        'quantity' => 10,
                    ],
                    [
                        'title' => 'Men\'s Traditional Shirt',
                        'description' => 'Handmade men\'s shirt with traditional Karen designs.',
                        'price' => 79.99,
                        'quantity' => 15,
                    ],
                ],
            ],
            // Handwoven Textiles
            [
                'category_name' => 'Handwoven Textiles',
                'products' => [
                    [
                        'title' => 'Traditional Table Runner',
                        'description' => 'Beautifully woven table runner with traditional patterns.',
                        'price' => 45.99,
                        'quantity' => 20,
                    ],
                    [
                        'title' => 'Decorative Wall Hanging',
                        'description' => 'Handwoven wall hanging featuring traditional Karen motifs.',
                        'price' => 89.99,
                        'quantity' => 8,
                    ],
                ],
            ],
            // Jewelry
            [
                'category_name' => 'Jewelry',
                'products' => [
                    [
                        'title' => 'Traditional Silver Necklace',
                        'description' => 'Handcrafted silver necklace with traditional Karen design.',
                        'price' => 159.99,
                        'quantity' => 5,
                    ],
                    [
                        'title' => 'Beaded Bracelet Set',
                        'description' => 'Set of three traditional beaded bracelets.',
                        'price' => 34.99,
                        'quantity' => 25,
                    ],
                ],
            ],
            // Home Decor
            [
                'category_name' => 'Home Decor',
                'products' => [
                    [
                        'title' => 'Decorative Cushion Cover',
                        'description' => 'Handwoven cushion cover with traditional patterns.',
                        'price' => 29.99,
                        'quantity' => 30,
                    ],
                    [
                        'title' => 'Traditional Basket',
                        'description' => 'Handwoven basket made using traditional techniques.',
                        'price' => 49.99,
                        'quantity' => 12,
                    ],
                ],
            ],
            // Bags & Accessories
            [
                'category_name' => 'Bags & Accessories',
                'products' => [
                    [
                        'title' => 'Traditional Shoulder Bag',
                        'description' => 'Handwoven shoulder bag with traditional Karen patterns.',
                        'price' => 69.99,
                        'quantity' => 15,
                    ],
                    [
                        'title' => 'Woven Belt',
                        'description' => 'Traditional Karen woven belt with classic patterns.',
                        'price' => 39.99,
                        'quantity' => 20,
                    ],
                ],
            ],
        ];

        foreach ($products as $categoryProducts) {
            $category = Category::where('category_name', $categoryProducts['category_name'])->first();
            
            foreach ($categoryProducts['products'] as $productData) {
                Product::create([
                    'category_id' => $category->id,
                    'title' => $productData['title'],
                    'slug' => Str::slug($productData['title']),
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'quantity' => $productData['quantity'],
                ]);
            }
        }
    }
} 
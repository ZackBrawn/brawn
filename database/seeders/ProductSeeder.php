<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Suppliers
        $suppliers = [
            [
                'name' => 'Apple Inc.',
                'contact_person' => 'Tim Cook',
                'phone' => '1-800-MY-APPLE',
                'address' => 'One Apple Park Way, Cupertino, CA',
            ],
            [
                'name' => 'Samsung Electronics',
                'contact_person' => 'Han Jong-hee',
                'phone' => '+82-2-2053-1114',
                'address' => 'Suwon, South Korea',
            ],
            [
                'name' => 'Nike Official',
                'contact_person' => 'John Donahoe',
                'phone' => '1-503-671-6453',
                'address' => 'One Bowerman Drive, Beaverton, OR',
            ],
            [
                'name' => 'Logitech International',
                'contact_person' => 'Bracken Darrell',
                'phone' => '+41 21 863 51 11',
                'address' => 'Apples, Switzerland',
            ],
            [
                'name' => 'Sony Corporation',
                'contact_person' => 'Kenichiro Yoshida',
                'phone' => '+81-3-6748-2111',
                'address' => 'Minato, Tokyo, Japan',
            ],
            [
                'name' => 'Adidas AG',
                'contact_person' => 'Bjørn Gulden',
                'phone' => '+49-9132-84-0',
                'address' => 'Herzogenaurach, Germany',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::updateOrCreate(['name' => $supplierData['name']], $supplierData);
        }

        // 2. Seed Categories
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Latest gadgets and tech devices.',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?q=80&w=1000',
            ],
            [
                'name' => 'Fashion',
                'description' => 'Trendy clothing and apparel.',
                'image' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?q=80&w=1000',
            ],
            [
                'name' => 'Accessories',
                'description' => 'Essential add-ons for your lifestyle.',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1000',
            ],
            [
                'name' => 'Home Appliances',
                'description' => 'Smart solutions for your home.',
                'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=1000',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => Str::slug($categoryData['name'])],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'image' => $categoryData['image'],
                ]
            );
        }

        // 3. Seed Payment Methods
        $paymentMethods = [
            [
                'name' => 'BCA Transfer',
                'account_name' => 'PT Brawn Indonesia',
                'account_number' => '8830123456',
                'is_active' => true,
            ],
            [
                'name' => 'Mandiri Transfer',
                'account_name' => 'PT Brawn Indonesia',
                'account_number' => '1230009876543',
                'is_active' => true,
            ],
            [
                'name' => 'GoPay',
                'account_name' => 'Brawn Store',
                'account_number' => '081234567890',
                'is_active' => true,
            ],
            [
                'name' => 'OVO',
                'account_name' => 'Brawn Store',
                'account_number' => '081234567890',
                'is_active' => true,
            ],
        ];

        foreach ($paymentMethods as $pmData) {
            PaymentMethod::updateOrCreate(['name' => $pmData['name']], $pmData);
        }

        // 4. Seed Products
        $electronics = Category::where('name', 'Electronics')->first();
        $fashion = Category::where('name', 'Fashion')->first();
        $accessories = Category::where('name', 'Accessories')->first();
        $home = Category::where('name', 'Home Appliances')->first();

        $apple = Supplier::where('name', 'Apple Inc.')->first();
        $samsung = Supplier::where('name', 'Samsung Electronics')->first();
        $nike = Supplier::where('name', 'Nike Official')->first();
        $logitech = Supplier::where('name', 'Logitech International')->first();
        $sony = Supplier::where('name', 'Sony Corporation')->first();
        $adidas = Supplier::where('name', 'Adidas AG')->first();

        $products = [
            [
                'category_id' => $electronics->id,
                'supplier_id' => $apple->id,
                'name' => 'iPhone 15 Pro',
                'description' => 'The ultimate iPhone with titanium design and A17 Pro chip.',
                'price' => 18999000,
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1696446701796-da61225697cc?q=80&w=1000',
            ],
            [
                'category_id' => $electronics->id,
                'supplier_id' => $samsung->id,
                'name' => 'Galaxy S24 Ultra',
                'description' => 'The power of Galaxy AI in your hands.',
                'price' => 21999000,
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?q=80&w=1000',
            ],
            [
                'category_id' => $fashion->id,
                'supplier_id' => $nike->id,
                'name' => 'Air Jordan 1 Retro High',
                'description' => 'The sneaker that started it all.',
                'price' => 2800000,
                'stock' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1584735175315-9d5df23860e6?q=80&w=1000',
            ],
            [
                'category_id' => $accessories->id,
                'supplier_id' => $logitech->id,
                'name' => 'MX Master 3S',
                'description' => 'Performance wireless mouse with quiet clicks.',
                'price' => 1500000,
                'stock' => 100,
                'image_url' => 'https://images.unsplash.com/photo-1631553127988-3098f986478d?q=80&w=1000',
            ],
            [
                'category_id' => $electronics->id,
                'supplier_id' => $apple->id,
                'name' => 'MacBook Air M3',
                'description' => 'Lean, mean, M3 machine.',
                'price' => 17499000,
                'stock' => 20,
                'image_url' => 'https://images.unsplash.com/photo-1517336714460-d1b16cd3e054?q=80&w=1000',
            ],
            [
                'category_id' => $electronics->id,
                'supplier_id' => $sony->id,
                'name' => 'Sony WH-1000XM5',
                'description' => 'Industry-leading noise canceling headphones.',
                'price' => 5999000,
                'stock' => 40,
                'image_url' => 'https://images.unsplash.com/photo-1648447226217-04024d92003c?q=80&w=1000',
            ],
            [
                'category_id' => $accessories->id,
                'supplier_id' => $apple->id,
                'name' => 'Apple Watch Ultra 2',
                'description' => 'The most rugged and capable Apple Watch.',
                'price' => 15999000,
                'stock' => 25,
                'image_url' => 'https://images.unsplash.com/photo-1664144841993-e18a6df4ac99?q=80&w=1000',
            ],
            [
                'category_id' => $fashion->id,
                'supplier_id' => $adidas->id,
                'name' => 'Adidas Ultraboost 1.0',
                'description' => 'The ultimate running shoe for comfort and energy return.',
                'price' => 3000000,
                'stock' => 60,
                'image_url' => 'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?q=80&w=1000',
            ],
            [
                'category_id' => $electronics->id,
                'supplier_id' => $samsung->id,
                'name' => 'Samsung Odyssey G9',
                'description' => '49-inch curved gaming monitor for immersive experience.',
                'price' => 25000000,
                'stock' => 10,
                'image_url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?q=80&w=1000',
            ],
            [
                'category_id' => $home->id,
                'supplier_id' => $sony->id,
                'name' => 'Sony Bravia XR A80L',
                'description' => 'Cognitive Processor XR with beautiful OLED contrast.',
                'price' => 35000000,
                'stock' => 5,
                'image_url' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?q=80&w=1000',
            ],
            [
                'category_id' => $accessories->id,
                'supplier_id' => $logitech->id,
                'name' => 'Keychron K2 V2',
                'description' => 'A compact 75% layout wireless mechanical keyboard.',
                'price' => 1200000,
                'stock' => 80,
                'image_url' => 'https://images.unsplash.com/photo-1595225476474-87563907a212?q=80&w=1000',
            ],
            [
                'category_id' => $fashion->id,
                'supplier_id' => $nike->id,
                'name' => 'Nike Sportswear Tech Fleece',
                'description' => 'Premium warmth and elevated style without the bulk.',
                'price' => 1800000,
                'stock' => 45,
                'image_url' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?q=80&w=1000',
            ],
            [
                'category_id' => $electronics->id,
                'supplier_id' => $apple->id,
                'name' => 'iPad Pro M2',
                'description' => 'Astonishing performance. Incredibly advanced displays.',
                'price' => 15499000,
                'stock' => 20,
                'image_url' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?q=80&w=1000',
            ],
            [
                'category_id' => $electronics->id,
                'supplier_id' => $sony->id,
                'name' => 'PlayStation 5 Slim',
                'description' => 'Experience lightning-fast loading with an ultra-high-speed SSD.',
                'price' => 8999000,
                'stock' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?q=80&w=1000',
            ],
            [
                'category_id' => $accessories->id,
                'supplier_id' => $logitech->id,
                'name' => 'Logitech G Pro X Superlight',
                'description' => 'The world\'s lightest, fastest pro gaming mouse.',
                'price' => 2100000,
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1629429408209-1f912961dbd8?q=80&w=1000',
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['slug' => Str::slug($productData['name'])],
                [
                    'category_id' => $productData['category_id'],
                    'supplier_id' => $productData['supplier_id'],
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'image_url' => $productData['image_url'],
                ]
            );
        }
    }
}

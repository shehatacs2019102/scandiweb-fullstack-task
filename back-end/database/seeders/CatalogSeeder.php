<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    /**
     * Load the catalog from database/data/data.json. Skipped when categories
     * already exist, so it is safe to run on every deploy.
     */
    public function run(): void
    {
        if (Category::exists()) {
            $this->command?->info('Catalog already seeded, skipping.');

            return;
        }

        $data = json_decode(file_get_contents(database_path('data/data.json')), true)['data'];

        DB::transaction(function () use ($data) {
            foreach ($data['categories'] as $category) {
                Category::create(['name' => $category['name']]);
            }

            $categoryIds = Category::pluck('id', 'name');

            foreach ($data['products'] as $item) {
                $product = Product::create([
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'in_stock' => $item['inStock'],
                    'gallery' => json_encode($item['gallery']),
                    'category_id' => $categoryIds[$item['category']],
                    'brand' => $item['brand'],
                ]);

                foreach ($item['attributes'] as $set) {
                    $attribute = Attribute::firstOrCreate(
                        ['name' => $set['name']],
                        ['type' => $set['type']]
                    );

                    foreach ($set['items'] as $option) {
                        $value = $attribute->values()->firstOrCreate(
                            ['value' => $option['value']],
                            ['display_value' => $option['displayValue']]
                        );

                        $product->attributeValues()->attach($value->id);
                    }
                }

                foreach ($item['prices'] as $price) {
                    $product->price()->create([
                        'amount' => $price['amount'],
                        'currency_label' => $price['currency']['label'],
                        'currency_symbol' => $price['currency']['symbol'],
                    ]);
                }
            }
        });
    }
}

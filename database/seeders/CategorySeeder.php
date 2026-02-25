<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['en' => 'Toner Cartridges', 'ar' => 'خراطيش الحبر'],
            ['en' => 'Ink Cartridges', 'ar' => 'خراطيش الحبر السائل'],
            ['en' => 'Paper', 'ar' => 'الورق'],
            ['en' => 'Office Supplies', 'ar' => 'لوازم المكتب'],
        ];

        foreach ($categories as $index => $names) {
            $category = Category::create([
                'parent_id' => null,
                'sort_order' => $index + 1,
                'is_active' => true,
                'slug' => Str::slug($names['en']),
            ]);
            CategoryTranslation::create([
                'category_id' => $category->id,
                'locale' => 'en',
                'name' => $names['en'],
                'slug' => Str::slug($names['en']),
            ]);
            CategoryTranslation::create([
                'category_id' => $category->id,
                'locale' => 'ar',
                'name' => $names['ar'],
                'slug' => Str::slug($names['ar']),
            ]);
        }
    }
}

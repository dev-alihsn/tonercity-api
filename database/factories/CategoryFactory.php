<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    protected bool $createTranslations = true;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Configure the factory to create translations after creating the category.
     * Can be disabled by calling withoutTranslations().
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Category $category) {
            // Only create translations if enabled
            if ($this->createTranslations) {
                $enName = fake()->words(2, true);
                $arName = $this->arabicCategoryName();

                // English translation
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => 'en',
                    'name' => $enName,
                    'slug' => Str::slug($enName),
                    'description' => fake()->sentence(),
                ]);

                // Arabic translation
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => 'ar',
                    'name' => $arName,
                    'slug' => Str::slug($arName),
                    'description' => $this->arabicCategoryDescription(),
                ]);
            }
        });
    }

    /**
     * Skip automatic translation creation (useful for tests that create translations manually).
     */
    public function withoutTranslations(): static
    {
        return tap($this, function () {
            $this->createTranslations = false;
        });
    }

    /**
     * Get a random Arabic category name.
     */
    private function arabicCategoryName(): string
    {
        $names = [
            'ورق الطباعة',
            'حبر وتونر الطابعات',
            'آلات الطباعة',
            'أجهزة المسح الضوئي',
            'مستلزمات المكاتب',
            'معدات الطباعة',
            'أوراق ملونة',
            'حبر السائل',
            'خراطيش الحبر',
            'طابعات ملونة',
            'ورق مقوى',
            'مستلزمات الطباعة المتقدمة',
            'حلول الطباعة الاحترافية',
            'أدوات التطبيق الطباعي',
        ];

        return fake()->randomElement($names);
    }

    /**
     * Get a random Arabic category description.
     */
    private function arabicCategoryDescription(): string
    {
        $descriptions = [
            'تصنيف متخصص في منتجات الطباعة عالية الجودة',
            'جميع احتياجاتك من المستلزمات الطباعية في مكان واحد',
            'منتجات موثوقة وأسعار تنافسية',
            'خدمة عملاء ممتازة ودعم فني متواصل',
            'منتجات أصلية مع ضمان شامل',
            'توصيل سريع وآمن إلى جميع المحافظات',
        ];

        return fake()->randomElement($descriptions);
    }

    public function childOf(Category $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
}

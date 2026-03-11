<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    protected bool $createTranslations = true;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => 'SKU-'.strtoupper(fake()->unique()->bothify('???-####')),
            'price' => fake()->randomFloat(2, 10, 500),
            'thumbnail_id' => null,
            'vendor_id' => null,
            'is_active' => true,
        ];
    }

    /**
     * Configure the factory to create translations after creating the product.
     * Can be disabled by calling withoutTranslations().
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            // Only create translations if enabled
            if ($this->createTranslations) {
                $enTitle = fake()->words(3, true);
                $arTitle = $this->arabicProductName();

                // English translation
                ProductTranslation::create([
                    'product_id' => $product->id,
                    'locale' => 'en',
                    'title' => $enTitle,
                    'slug' => Str::slug($enTitle),
                    'description' => fake()->sentences(3, true),
                ]);

                // Arabic translation
                ProductTranslation::create([
                    'product_id' => $product->id,
                    'locale' => 'ar',
                    'title' => $arTitle,
                    'slug' => Str::slug($arTitle),
                    'description' => $this->arabicDescription(),
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
     * Get a random Arabic product name.
     */
    private function arabicProductName(): string
    {
        $names = [
            'ورق طباعة احترافي A4',
            'حبر طابعة عالي الجودة',
            'تونر أسود عالي الأداء',
            'خرطوش حبر ملون أصلي',
            'ورق فوتو لامع 4x6',
            'طابعة ليزر سريعة',
            'ماسح ضوئي محمول',
            'حبر صيانة الطابعة',
            'ورق رقم 11 أبيض',
            'تونر ملون متوافق',
            'فيلم ورقي شفاف',
            'ورق مقوى قوي الملمس',
            'حبر صبغة أصلي مستورد',
            'ورق حرير ناعم الملمس',
            'تونر إعادة تعبئة موثوق',
        ];

        return fake()->randomElement($names);
    }

    /**
     * Get a random Arabic product description.
     */
    private function arabicDescription(): string
    {
        $descriptions = [
            'منتج عالي الجودة مصمم خصيصاً للطباعة الاحترافية',
            'يتمتع بأداء ممتازة وكفاءة عالية في الاستخدام',
            'متوافق تماماً مع جميع أنواع الطابعات الحديثة',
            'يوفر نتائج طباعة واضحة وحادة جداً',
            'مواد خام أصلية موثوقة من الشركات العالمية',
            'آمن على البيئة وصحة المستخدم',
            'يدوم طويلاً ويوفر قيمة مالية كبيرة',
            'منتج معتمد ومضمون من قبل الشركات الموثوقة',
        ];

        return fake()->randomElement($descriptions);
    }

    public function withThumbnail(): static
    {
        return $this->state(function (array $attributes) {
            $media = Media::factory()->image()->create();

            return [
                'thumbnail_id' => $media->id,
            ];
        });
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

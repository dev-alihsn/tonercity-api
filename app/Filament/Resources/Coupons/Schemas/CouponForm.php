<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label(__('coupon.code')),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->label(__('coupon.discount')),
                TextInput::make('usage_limit')
                    ->numeric()
                    ->label(__('coupon.usage_limit')),
                TextInput::make('usage_count')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->default(0)
                    ->label(__('coupon.usage_count')),
                DateTimePicker::make('valid_from')
                    ->label(__('coupon.valid_from')),
                DateTimePicker::make('valid_until')
                    ->label(__('coupon.valid_until')),
                Toggle::make('is_active')
                    ->required()
                    ->label(__('coupon.is_active')),
            ]);
    }
}

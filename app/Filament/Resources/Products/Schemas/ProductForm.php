<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->label(__('product.vendor')),
                TextInput::make('sku')
                    ->label(__('product.sku'))
                    ->label('SKU')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->label(__('product.price')),
                TextInput::make('sale_price')
                    ->numeric()
                    ->prefix('$')
                    ->label(__('product.sale_price')),
                Select::make('thumbnail_id')
                    ->relationship('thumbnail', 'id')
                    ->label(__('product.thumbnail')),
                Toggle::make('is_active')
                    ->required()
                    ->label(__('product.is_active')),
                Repeater::make('translations')
                    ->relationship('translations')
                    ->label(__('product.translations'))
                    ->schema([
                        Select::make('locale')
                            ->options([
                                'en' => 'English',
                                'ar' => 'Arabic',
                            ])
                            ->required()
                            ->label(__('product.locale')),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label(__('product.name')),
                        Textarea::make('description')
                            ->columnSpanFull()
                            ->label(__('product.description')),
                    ])
                    ->columns(2)
                    ->defaultItems(0),
            ]);
    }
}

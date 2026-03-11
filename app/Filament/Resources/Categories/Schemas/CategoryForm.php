<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->relationship('parent', 'id')
                    ->label(__('category.parent_category')),
                Toggle::make('is_active')
                    ->required()
                    ->label(__('category.is_active')),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label(__('category.sort_order')),
                Repeater::make('translations')
                    ->relationship('translations')
                    ->label(__('category.translations'))
                    ->schema([
                        Select::make('locale')
                            ->options([
                                'en' => 'English',
                                'ar' => 'Arabic',
                            ])
                            ->required()
                            ->label(__('category.locale')),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label(__('category.name')),
                    ])
                    ->columns(2)
                    ->defaultItems(0),
            ]);
    }
}

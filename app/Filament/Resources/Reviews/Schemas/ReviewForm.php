<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label(__('review.user')),
                Select::make('product_id')
                    ->relationship('product', 'id')
                    ->required()
                    ->label(__('review.product')),
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label(__('review.rating')),
                Textarea::make('comment')
                    ->columnSpanFull()
                    ->label(__('review.comment')),
            ]);
    }
}

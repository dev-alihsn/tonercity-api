<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'id')
                    ->required()
                    ->label(__('question.product')),
                Textarea::make('content')
                    ->columnSpanFull()
                    ->label(__('question.content')),
                Textarea::make('answer')
                    ->columnSpanFull()
                    ->label(__('question.answer')),
            ]);
    }
}

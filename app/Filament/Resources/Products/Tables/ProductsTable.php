<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            \Filament\Tables\Columns\TextColumn::make('id')
                ->label(__('ID'))
                ->sortable()
                ->searchable(),

            \Filament\Tables\Columns\TextColumn::make('name')
                ->label(__('shop.product'))
                ->sortable()
                ->searchable(),

            \Filament\Tables\Columns\TextColumn::make('price')
                ->label(__('Price'))
                ->money('USD', true)
                ->sortable(),

            \Filament\Tables\Columns\TextColumn::make('created_at')
                ->label(__('Created At'))
                ->dateTime('Y-m-d H:i')
                ->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

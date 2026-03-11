<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('product.name'))
                    ->getStateUsing(fn ($record) => $record->getTranslation(app()->getLocale())?->name ?? 'Untitled')
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('translations', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('vendor.name')
                    ->label(__('product.vendor'))
                    ->searchable(),
                TextColumn::make('sku')
                    ->label(__('product.sku'))
                    ->searchable(),
                TextColumn::make('price')
                    ->label(__('product.price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label(__('product.sale_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('thumbnail.id')
                    ->label(__('product.thumbnail'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('product.is_active'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
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

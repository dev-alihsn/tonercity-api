<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('category.name'))
                    ->getStateUsing(fn ($record) => $record->getTranslation(app()->getLocale())?->name ?? 'Untitled')
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('translations', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('parent_name')
                    ->label(__('category.parent_category'))
                    ->getStateUsing(fn ($record) => $record->parent?->getTranslation(app()->getLocale())?->name ?? '-'),
                IconColumn::make('is_active')
                    ->label(__('category.is_active'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('category.sort_order'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('logo_id')
                    ->label(__('category.logo'))
                    ->numeric()
                    ->sortable(),
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

<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->label(__('coupon.code')),
                TextColumn::make('discount')
                    ->numeric()
                    ->sortable()
                    ->label(__('coupon.discount')),
                TextColumn::make('usage_limit')
                    ->numeric()
                    ->sortable()
                    ->label(__('coupon.usage_limit')),
                TextColumn::make('usage_count')
                    ->numeric()
                    ->sortable()
                    ->label(__('coupon.usage_count')),
                TextColumn::make('valid_from')
                    ->dateTime()
                    ->sortable()
                    ->label(__('coupon.valid_from')),
                TextColumn::make('valid_until')
                    ->dateTime()
                    ->sortable()
                    ->label(__('coupon.valid_until')),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('coupon.is_active')),
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

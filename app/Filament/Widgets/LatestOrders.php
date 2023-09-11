<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([

                TextColumn::make('number')
                ->label('Número')
                ->searchable()
                ->sortable(),

                TextColumn::make('customer.name')
                ->label('Cliente')
                ->searchable()
                ->sortable()
                ->toggleable(),

                TextColumn::make('status')
                ->label('Estatus')
                ->searchable()
                ->sortable(),

                TextColumn::make('created_at')
                ->label('Fecha de Orden')
                ->date(),
            ])
            ->filters([
                SelectFilter::make('status')
                ->label('Estatus')
                ->options([
                            'pendiente' => OrderStatusEnum::PENDIENTE->value,
                            'procesando' => OrderStatusEnum::PROCESANDO->value,
                            'completado' => OrderStatusEnum::COMPLETADO->value,
                            'rechazado' => OrderStatusEnum::RECHAZADO->value,
                ])
            ])
            ->actions([
                // ActionGroup::make([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make()
                // ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Crear orden'),
            ]);
    }
}

<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatusEnum;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '15s';

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Clientes', Customer::count())
            ->description('Aumento de clientes')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success')
            ->chart([7,3,4, 6, 8, 13, 3, 5, 4,10]),

            Stat::make('Total Productos', Product::count())
            ->description('Total de productos en App')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('danger')
            ->chart([2,7,3,5,8,9,10,13]),

            Stat::make('Órdenes Pendientes', Order::where('status', OrderStatusEnum::PENDIENTE->value)->count())
            ->description('Total de productos en la App')
            ->descriptionColor('heroicon-m-arrow-trending-down')
            ->color('danger')
            ->chart([7,2,3,5,8,9,4]),

        ];
    }
}

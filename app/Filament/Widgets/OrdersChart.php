<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrdersChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Número de órdenes según su estatus';

    protected function getData(): array
    {
        $data = Order::select('status', DB::raw('count(*) as count'))->groupBy('status')
        ->pluck('count','status')
        ->toArray();

        return [
            'datasets' =>[
                [
                    'label' => 'Órdenes',
                    'data' => array_values($data)
                ]
                ],
                'labels' => OrderStatusEnum::cases()
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

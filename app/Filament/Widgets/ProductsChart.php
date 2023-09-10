<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Colocación de Productos por mes';

    protected static ?int $sort = 3;

    protected function getData(): array
    {

        $data = $this->getProductsPerMonth();

        return [
            'datasets' => [

                [
                    'label' => 'Blog posts created',
                    'data' => $data['productsPerMonth']

                ]

            ],
            'labels' => $data['months']

        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

     /**
     * Este método retorna matriz de datos para la generación de Gráficos
     *
     * Se implementa el método whereMonth() el cual no trabaja con colecciones
     * para poder implementarlo sin error, se debe pasar por referencia
     * el argumento variable $getProductsPerMonth dentro de map().
     * whereMonth() solo es reconocible dentro del método map().
     * Recuerden que el signo '&' se antepone a la variable que se desea
     * pasar por referencia. En este método ha debido ser así puesto que
     * se definió la variable como collection antes del uso de map().
     *
     * @access private
     * @return array<string, mixed>
     **/
    private function getProductsPerMonth(): array
    {
        $now = Carbon::now();

        $productsPerMonth = [];

        $months = collect(range(1, 12))->map(function($month) use ($now, &$productsPerMonth){

            $count = Product::whereMonth('created_at', Carbon::parse($now->month($month)->format('Y-m')))->count();

            $productsPerMonth[] = $count;

            return $now->month($month)->format('M');
        })->toArray();

        return [
            'productsPerMonth' => $productsPerMonth,
            'months' => $months
        ];
    }
}

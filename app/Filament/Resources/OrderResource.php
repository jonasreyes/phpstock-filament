<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Comercio';

    protected static ?string $navigationLabel = 'Ordenes';

    protected static ?string $activeNavigationIcon = 'heroicon-s-shopping-bag';

    protected static ?string $heading = 'Ordenes';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '=', 'processing')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', '=', 'processing')->count() > 10
            ? 'warning'
            : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Detalles de Orden')
                    ->schema([
                        TextInput::make('number')
                        ->label('Número')
                        ->default('OR-' . random_int(100000, 9999999))
                        ->disabled()
                        ->dehydrated()
                        ->required(),

                        Select::make('customer_id')
                        ->label('Cliente')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->required(),

                        TextInput::make('shipping_price')
                        ->label('Costos de envío')
                        ->dehydrated()
                        ->numeric()
                        ->required(),

                        Select::make('type')
                        ->label('Tipo')
                        ->options([
                            'pending' => OrderStatusEnum::PENDING->value,
                            'processig' => OrderStatusEnum::PROCESSING->value,
                            'completed' => OrderStatusEnum::COMPLETED->value,
                            'declined' => OrderStatusEnum::DECLINED->value,
                        ])->required(),

                        MarkdownEditor::make('notes')
                        ->label('Notas')
                        ->columnSpanFull()
                    ])->columns(2),

                    Step::make('Artículos de Orden')
                    ->schema([
                        Repeater::make('items')
                        ->relationship()
                        ->schema([

                            Select::make('product_id')
                            ->label('Producto')
                            ->options(Product::query()->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($state, Forms\Set $set) =>
                            $set('unit_price', Product::find($state)?->price ?? 0)),

                            TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->live()
                            ->dehydrated()
                            ->default(1)
                            ->required(),

                            TextInput::make('unit_price')
                            ->label('Precio')
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->required(),

                            Forms\Components\Placeholder::make('total_price')
                            ->label('Precio Total')
                            ->content(function($get){
                                return $get('quantity') * $get('unit_price');
                            })
                        ])->columns(4)
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                //
            ])
            ->actions([
                ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Crear orden'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

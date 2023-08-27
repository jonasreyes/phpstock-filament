<?php

namespace App\Filament\Resources;

use App\Enums\ProductTypeEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationGroup = 'Comercio';

    protected static ?string $navigationLabel = 'Productos';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                ->schema([
                    Section::make()
                    ->schema([
                        TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->unique(ignoreRecord: true) // ignoreRecord evita el error de verificar como unico el campo cuando se está modificando el registro.
                        ->afterStateUpdated(function(string $operation, $state, Forms\Set $set){
                            if($operation !== 'create'){
                                return;
                            }
                            $set('slug', Str::slug($state));

                        }),

                        TextInput::make('slug')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->unique(Product::class, 'slug', ignoreRecord: true),
                        MarkdownEditor::make('description')->columnSpan('full'),
                    ])->columns(2),

                    Section::make('Precio & Inventario')
                    ->schema([
                        TextInput::make('sku')
                        ->label("SKU (Unidad Mant. Stock)"),

                        TextInput::make('price')
                        ->numeric()
                        ->rules('regex:/^\d{1,6}(\.\d{0,2})?$/')
                        ->required(),

                        TextInput::make('quantity')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),

                        Select::make('type')
                        ->options([
                            'downloadable' => ProductTypeEnum::DOWNLOADABLE->value,
                            'deliverable' => ProductTypeEnum::DELIVERABLE->value,
                        ])->required()
                    ])->columns(2)
                ]),

                Group::make()
                ->schema([
                    Section::make('Status')
                    ->schema([
                        Toggle::make('is_visible')
                        ->label('Visibilidad')
                        ->helperText('Habilita o inhabilita la visibilidad del producto')
                        ->default(true),

                        Toggle::make('is_featured')
                        ->label('Característica')
                        ->helperText('Habilita o inhabilita el estado de productos destacados'),

                        DatePicker::make('published_at')
                        ->label('Disponibilidad')
                        ->default(now())
                        ->columnSpan('full')
                    ]),

                    Section::make('Imagen')
                    ->schema([
                        FileUpload::make('image')
                        ->directory('form-attachments')
                        ->preserveFilenames()
                        ->image()
                        ->imageEditor()
                    ])->collapsible(),


                    Section::make('Asociaciones')
                    ->schema([
                        Select::make('brand_id')
                        ->relationship('brand', 'name')
                    ])->collapsible()
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('name')
                ->searchable()
                ->sortable(),

                TextColumn::make('brand.name')
                ->searchable()
                ->sortable()
                ->toggleable(),

                IconColumn::make('is_visible')
                ->sortable()
                ->toggleable()
                ->label('Visibilidad')
                ->boolean(),
                TextColumn::make('price')
                ->sortable()
                ->toggleable(),

                TextColumn::make('quantity')
                ->sortable()
                ->toggleable(),

                TextColumn::make('published_at')
                ->date()
                ->sortable(),

                TextColumn::make('type'),
            ])
            ->filters([
                TernaryFilter::make('is_visible')
                ->label('Visibilidad')
                ->boolean()
                ->trueLabel('Solo Productos Visibles')
                ->falseLabel('Solo Productos Ocultos')
                ->native(false),

                SelectFilter::make('brand')
                ->relationship('brand', 'name')
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
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
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

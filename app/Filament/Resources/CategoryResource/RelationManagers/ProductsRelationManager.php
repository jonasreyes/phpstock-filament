<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Enums\ProductTypeEnum;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form->schema([

                Forms\Components\Tabs::make('Productos')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Información')
                    ->schema([
                        TextInput::make('name')
                        ->label('Nombre')
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
                        MarkdownEditor::make('description')->columnSpan('full')
                        ->label('Descripción'),
                    ])->columns(),

                    Forms\Components\Tabs\Tab::make('Precio & Inventario')
                    ->schema([
                        TextInput::make('sku')
                        ->label("SKU (Unidad Mant. Stock)"),

                        TextInput::make('price')
                        ->label('Precio')
                        ->numeric()
                        ->rules('regex:/^\d{1,6}(\.\d{0,2})?$/')
                        ->required(),

                        TextInput::make('quantity')
                        ->label('Cantidad')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),

                        Select::make('type')
                        ->label('Tipo')
                        ->options([
                            'downloadable' => ProductTypeEnum::DOWNLOADABLE->value,
                            'deliverable' => ProductTypeEnum::DELIVERABLE->value,
                        ])->required()
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Información Adicional')
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
                        ->default(now()),

                        // Un pequeño detalle que pudo generar dolores de cabeza:
                        // el formulario original de creación de productos incorpora
                        // listas desplegables de baind (marca) y category (categoría)
                        // el formulario dentro de un RelationManager no requiere que se visibilice
                        // la lista de opciones de cagegoria si este es un formulario relacionado
                        // entre productos-categorias, solo se debe copiar la lista de brand o marca.
                        // si se estuviese en el RelationManager de Brain se omitiria la lista de
                        // marca pro si se incorporaría la lista de Categoría.
                        Select::make('brand_id')
                        ->relationship('brand', 'name'),


                        FileUpload::make('image')
                        ->directory('form-attachments')
                        ->preserveFilenames()
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull()
                    ])->columns(2)

                ])->columnSpanFull()
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')->heading('Productos por categoría')
            ->columns([
                ImageColumn::make('image')
                ->label('Imagen'),

                TextColumn::make('name')
                ->label('Nombre')
                ->searchable()
                ->sortable(),

                TextColumn::make('brand.name')
                ->label('Marca')
                ->searchable()
                ->sortable()
                ->toggleable(),

                IconColumn::make('is_visible')
                ->sortable()
                ->toggleable()
                ->label('Visibilidad')
                ->boolean(),
                TextColumn::make('price')
                ->label('Precio')
                ->sortable()
                ->toggleable(),

                TextColumn::make('quantity')
                ->label('Cantidad')
                ->sortable()
                ->toggleable(),

                TextColumn::make('published_at')
                ->label('Fecha Publicación')
                ->date()
                ->sortable(),

                TextColumn::make('type')->label('Tipo'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Crear producto'),
            ])
            ->actions([
                ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}

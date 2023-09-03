<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $activeNavigationIcon = 'heroicon-s-tag';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Categorias';

    protected static ?string $navigationGroup = 'Comercio';

    // protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()
                ->schema([
                    Section::make([

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

                        MarkdownEditor::make('description')
                        ->label('Descripción')
                        ->columnSpanFull()
                    ])->columns(2)
                ]),

                Group::make()
                ->schema([
                    Section::make('Estatus')
                    ->schema([
                        Toggle::make('is_visible')
                        ->label('Visibilidad')
                        ->helperText('Habilita o deshabilita la visibilidad de la categoría')
                        ->default(true),

                        Select::make('parent_id')
                        ->label('Padre')
                        // Se comenta porque aún no está creado este modelo "parent"
                        ->relationship('parent', 'name')
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Nombre')
                ->sortable()
                ->searchable(),

                TextColumn::make('parent.name')
                ->label('Padre')
                ->searchable()
                ->sortable(),

                IconColumn::make('is_visible')
                ->label('Visibilidad')
                ->boolean()
                ->sortable(),

                TextColumn::make('updated_at')
                ->date()
                ->label('Fecha Actualización')
                ->sortable(),
            ])

            ->filters([
                //
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

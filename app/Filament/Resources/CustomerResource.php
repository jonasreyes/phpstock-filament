<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Comercio';

    protected static ?string $navigationLabel = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    TextInput::make('name')
                    ->label('Nombre')
                    ->maxValue(50)
                    ->required(),

                    TextInput::make('email')
                    ->label('Correo Electronico')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true),

                    TextInput::make('phone')
                    ->label('Nro. Teléfono')
                    ->maxValue(50),

                    DatePicker::make('date_of_birth')
                    ->label('Fecha de Nacimiento'),

                    TextInput::make('city')
                    ->label('Ciudad')
                    ->required(),

                    TextInput::make('zip_code')
                    ->label('Código Postal')
                    ->required(),

                    TextInput::make('address')
                    ->label('Dirección')
                    ->required()
                    ->columnSpanFull()
                ])->columns(2)
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

                TextColumn::make('email')
                ->label('email')
                ->sortable()
                ->searchable(),

                TextColumn::make('phone')
                ->label('Teléfono')
                ->searchable(),

                TextColumn::make('city')
                ->label('Ciudad')
                ->sortable()
                ->searchable(),

                TextColumn::make('date_of_birth')
                ->label('Fecha de Nacimiento')
                ->date()
                ->sortable()

            ])
            ->filters([
                //
            ])
            ->actions([

                ActionGroup::make([
                    ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    DeleteAction::make()
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}

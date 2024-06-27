<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovementResource\Pages;
use App\Filament\Resources\MovementResource\RelationManagers;
use App\Models\Movement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MovementResource extends Resource
{
    protected static ?string $model = Movement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Radio::make('type')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'ajuste' => 'Ajuste',
                    ])
                    ->reactive()
                    ->default('entrada')
                    ->required(),

                Forms\Components\Placeholder::make('Note')
                    ->label('Nota')
                    // ->content('jajajajajjajja'),
                    ->content(function ($get) {
                        $type = $get('type');
                        $quantity = $get('quantity');
                        $quantityText = $quantity ? " $quantity" : "";
                        return match ($type) {
                            'entrada' => 'Se sumarán' . $quantityText . ' a la cantidad actual del artículo.',
                            'salida' => 'Se restarán' . $quantityText . ' a la cantidad actual del artículo.',
                            'ajuste' => 'La cantidad del artículo será ajustada a' . $quantityText . '.',
                            default => 'Seleccione una opción.',
                        };
                    }),
                Forms\Components\Select::make('product_id')
                    ->default(1)
                    ->preload()
                    ->searchable()
                    ->relationship('product','name')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->rule('min:0')
                    ->reactive()
                    ->default(null),
                Forms\Components\TextInput::make('person')
                    ->required()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('product_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('person')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListMovements::route('/'),
            'create' => Pages\CreateMovement::route('/create'),
            // 'edit' => Pages\EditMovement::route('/{record}/edit'),
        ];
    }
    public static function getNavigationIcon(): string {
        return 'heroicon-o-inbox-stack';
    }
    public static function getLabel(): string
    {
        return 'Movimiento';
    }

    public static function getPluralLabel(): string
    {
        return 'Movimientos';
    }
}

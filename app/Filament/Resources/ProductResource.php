<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre'),
                Forms\Components\TextInput::make('barcode')
                    ->maxLength(255)
                    ->default(null)
                    ->label('Código de barras'),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->default(null)
                    ->disabled(fn ($livewire) => $livewire->record ? $livewire->record->exists : false)
                    ->label('Cantidad'),
                Placeholder::make('Note')
                    ->content('Por favor, ingresa la cantidad inicial al crear el registro. Este campo no será editable después. sólo a traves de movimientos')
                    ->label('Nota'),
                Forms\Components\Select::make('unit_id')
                    ->default(1)
                    ->preload()
                    ->searchable()
                    ->relationship('unit','name')
                    ->required()
                    ->label('Unidad'),
                Forms\Components\Select::make('category_id')
                    ->default(1)
                    ->preload()
                    ->searchable()
                    ->relationship('category','name')
                    ->required()
                    ->label('Categoría'),
                Forms\Components\Toggle::make('active')
                    ->required()
                    ->label('Activo')
                    ->default(True),
                Textarea::make('description')
                    ->maxLength(255)
                    ->default(null)
                    ->label('Descripción'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table    
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('barcode')
                    ->searchable()
                    ->label('Código de barras')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->label('Descripción')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->label('Cantidad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->numeric()
                    ->label('Unidad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->label('Categoría')
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Creado')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Actualizado')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Categoría'),
                SelectFilter::make('active')
                    ->options([
                        '' => 'Todos',
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    ])
                    ->default('1')  // Establecer 'Activo' como valor predeterminado
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                //Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
    public static function getNavigationIcon(): string {
        return 'heroicon-o-light-bulb';
    }
    public static function getLabel(): string
    {
        return 'Artículo';
    }

    public static function getPluralLabel(): string
    {
        return 'Artículos';
    }
}

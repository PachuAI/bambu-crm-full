<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationGroup = 'Inventario';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Producto')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(150)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->unique(Producto::class, 'sku', ignoreRecord: true)
                            ->maxLength(50),
                            
                        Forms\Components\Select::make('marca_producto')
                            ->label('Marca')
                            ->options([
                                'BAMBU' => 'BAMBU',
                                'SAPHIRUS' => 'SAPHIRUS',
                                'OTRA' => 'OTRA',
                            ])
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Precios y Stock')
                    ->schema([
                        Forms\Components\TextInput::make('precio_base_l1')
                            ->label('Precio Base L1')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->step(0.01),
                            
                        Forms\Components\TextInput::make('stock_actual')
                            ->label('Stock Actual')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                            
                        Forms\Components\TextInput::make('peso_kg')
                            ->label('Peso (kg)')
                            ->numeric()
                            ->step(0.001)
                            ->suffix('kg'),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Características')
                    ->schema([
                        Forms\Components\Toggle::make('es_combo')
                            ->label('Es Combo')
                            ->default(false),
                            
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('marca_producto')
                    ->label('Marca')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'BAMBU' => 'success',
                        'SAPHIRUS' => 'info',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('precio_base_l1')
                    ->label('Precio L1')
                    ->money('ARS')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger')),
                    
                Tables\Columns\IconColumn::make('es_combo')
                    ->label('Combo')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('peso_kg')
                    ->label('Peso')
                    ->numeric(3)
                    ->suffix(' kg')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('marca_producto')
                    ->label('Marca')
                    ->options([
                        'BAMBU' => 'BAMBU',
                        'SAPHIRUS' => 'SAPHIRUS',
                        'OTRA' => 'OTRA',
                    ]),
                    
                Tables\Filters\Filter::make('sin_stock')
                    ->label('Sin Stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_actual', 0)),
                    
                Tables\Filters\Filter::make('es_combo')
                    ->label('Solo Combos')
                    ->query(fn (Builder $query): Builder => $query->where('es_combo', true)),
                    
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nombre')
            ->striped();
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

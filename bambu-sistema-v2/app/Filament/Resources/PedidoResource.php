<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Ventas';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Pedido')
                    ->schema([
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'borrador' => 'Borrador',
                                'confirmado' => 'Confirmado',
                                'en_reparto' => 'En Reparto',
                                'entregado' => 'Entregado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->default('borrador')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Descuentos y Montos')
                    ->schema([
                        Forms\Components\Select::make('nivel_descuento_id')
                            ->label('Nivel de Descuento')
                            ->relationship('nivelDescuento', 'nombre')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('monto_bruto')
                            ->label('Monto Bruto')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->required(),

                        Forms\Components\TextInput::make('monto_final')
                            ->label('Monto Final')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Logística')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_reparto')
                            ->label('Fecha de Reparto')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'borrador' => 'gray',
                        'confirmado' => 'info',
                        'en_reparto' => 'warning',
                        'entregado' => 'success',
                        'cancelado' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'borrador' => 'Borrador',
                        'confirmado' => 'Confirmado',
                        'en_reparto' => 'En Reparto',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                    }),

                Tables\Columns\TextColumn::make('monto_bruto')
                    ->label('Monto Bruto')
                    ->money('ARS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('monto_final')
                    ->label('Monto Final')
                    ->money('ARS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('descuento_aplicado')
                    ->label('Descuento')
                    ->money('ARS')
                    ->getStateUsing(fn (Pedido $record): float => $record->descuento_aplicado)
                    ->color('success'),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->sortable(),

                Tables\Columns\TextColumn::make('peso_total')
                    ->label('Peso Total')
                    ->getStateUsing(fn (Pedido $record): string => number_format($record->peso_total, 2).' kg')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fecha_reparto')
                    ->label('Fecha Reparto')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'confirmado' => 'Confirmado',
                        'en_reparto' => 'En Reparto',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                    ]),

                Tables\Filters\SelectFilter::make('cliente')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('fecha_reparto')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_reparto', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_reparto', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
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

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfiguracionResource\Pages;
use App\Models\Configuracion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConfiguracionResource extends Resource
{
    protected static ?string $model = Configuracion::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'clave';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Configuración')
                    ->schema([
                        Forms\Components\TextInput::make('clave')
                            ->required()
                            ->unique(Configuracion::class, 'clave', ignoreRecord: true)
                            ->maxLength(100)
                            ->columnSpan(2),

                        Forms\Components\Select::make('categoria')
                            ->options([
                                'general' => 'General',
                                'sistema' => 'Sistema',
                                'notificaciones' => 'Notificaciones',
                                'precios' => 'Precios',
                                'logistica' => 'Logística',
                                'integraciones' => 'Integraciones',
                            ])
                            ->default('general')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Valor y Tipo')
                    ->schema([
                        Forms\Components\Select::make('tipo')
                            ->options([
                                'string' => 'Texto',
                                'number' => 'Número',
                                'boolean' => 'Booleano',
                                'json' => 'JSON',
                            ])
                            ->default('string')
                            ->required()
                            ->native(false)
                            ->reactive(),

                        Forms\Components\TextInput::make('valor')
                            ->required()
                            ->maxLength(500)
                            ->columnSpan(2)
                            ->hint(fn ($get) => match ($get('tipo')) {
                                'boolean' => 'Valores: true, false, 1, 0',
                                'number' => 'Solo números decimales',
                                'json' => 'Formato JSON válido',
                                default => 'Texto libre'
                            }),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Configuración Adicional')
                    ->schema([
                        Forms\Components\Toggle::make('es_publico')
                            ->label('Es Público')
                            ->helperText('Las configuraciones públicas son accesibles sin autenticación')
                            ->default(false),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->maxLength(255)
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
                Tables\Columns\TextColumn::make('clave')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('categoria')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'gray',
                        'sistema' => 'info',
                        'notificaciones' => 'warning',
                        'precios' => 'success',
                        'logistica' => 'danger',
                        'integraciones' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color('secondary')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'string' => 'Texto',
                        'number' => 'Número',
                        'boolean' => 'Booleano',
                        'json' => 'JSON',
                        default => $state
                    }),

                Tables\Columns\TextColumn::make('valor')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) <= 50 ? null : $state;
                    })
                    ->copyable(),

                Tables\Columns\TextColumn::make('valor_parsed')
                    ->label('Valor Procesado')
                    ->getStateUsing(function (Configuracion $record) {
                        $valor = $record->valor_parsed;
                        if (is_bool($valor)) {
                            return $valor ? 'Verdadero' : 'Falso';
                        }
                        if (is_array($valor)) {
                            return 'Array ('.count($valor).' elementos)';
                        }

                        return is_string($valor) ? $valor : json_encode($valor);
                    })
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('es_publico')
                    ->label('Público')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) <= 50 ? null : $state;
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'general' => 'General',
                        'sistema' => 'Sistema',
                        'notificaciones' => 'Notificaciones',
                        'precios' => 'Precios',
                        'logistica' => 'Logística',
                        'integraciones' => 'Integraciones',
                    ]),

                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'string' => 'Texto',
                        'number' => 'Número',
                        'boolean' => 'Booleano',
                        'json' => 'JSON',
                    ]),

                Tables\Filters\TernaryFilter::make('es_publico')
                    ->label('Es Público')
                    ->boolean()
                    ->trueLabel('Solo públicas')
                    ->falseLabel('Solo privadas')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('categoria')
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
            'index' => Pages\ListConfiguracions::route('/'),
            'create' => Pages\CreateConfiguracion::route('/create'),
            'edit' => Pages\EditConfiguracion::route('/{record}/edit'),
        ];
    }
}

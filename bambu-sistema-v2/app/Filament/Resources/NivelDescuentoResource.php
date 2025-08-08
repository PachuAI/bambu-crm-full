<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NivelDescuentoResource\Pages;
use App\Models\NivelDescuento;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NivelDescuentoResource extends Resource
{
    protected static ?string $model = NivelDescuento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListNivelDescuentos::route('/'),
            'create' => Pages\CreateNivelDescuento::route('/create'),
            'edit' => Pages\EditNivelDescuento::route('/{record}/edit'),
        ];
    }
}

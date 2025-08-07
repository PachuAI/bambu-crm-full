<?php

namespace App\Filament\Resources\NivelDescuentoResource\Pages;

use App\Filament\Resources\NivelDescuentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNivelDescuentos extends ListRecords
{
    protected static string $resource = NivelDescuentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

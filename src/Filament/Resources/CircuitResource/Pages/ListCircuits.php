<?php

namespace Bishopm\Methodist\Filament\Resources\CircuitResource\Pages;

use Bishopm\Methodist\Filament\Resources\CircuitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCircuits extends ListRecords
{
    protected static string $resource = CircuitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

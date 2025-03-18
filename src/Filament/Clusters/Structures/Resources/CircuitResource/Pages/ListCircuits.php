<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource;
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

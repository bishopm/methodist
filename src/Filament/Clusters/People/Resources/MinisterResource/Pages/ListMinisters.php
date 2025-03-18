<?php

namespace Bishopm\Methodist\Filament\Clusters\People\Resources\MinisterResource\Pages;

use Bishopm\Methodist\Filament\Clusters\People\Resources\MinisterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMinisters extends ListRecords
{
    protected static string $resource = MinisterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

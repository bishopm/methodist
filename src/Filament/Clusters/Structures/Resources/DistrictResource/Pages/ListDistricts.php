<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources\DistrictResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Structures\Resources\DistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDistricts extends ListRecords
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

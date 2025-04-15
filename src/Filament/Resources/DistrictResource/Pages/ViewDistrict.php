<?php

namespace Bishopm\Methodist\Filament\Resources\DistrictResource\Pages;

use Bishopm\Methodist\Filament\Resources\DistrictResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewDistrict extends ViewRecord
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

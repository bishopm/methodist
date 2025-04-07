<?php

namespace Bishopm\Methodist\Filament\Resources\DistrictResource\Pages;

use Bishopm\Methodist\Filament\Resources\DistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDistrict extends EditRecord
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

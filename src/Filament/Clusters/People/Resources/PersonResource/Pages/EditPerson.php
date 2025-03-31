<?php

namespace Bishopm\Methodist\Filament\Clusters\People\Resources\PersonResource\Pages;

use Bishopm\Methodist\Filament\Clusters\People\Resources\PersonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerson extends EditRecord
{
    protected static string $resource = PersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

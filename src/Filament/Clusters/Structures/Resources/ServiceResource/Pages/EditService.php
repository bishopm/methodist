<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources\ServiceResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Structures\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

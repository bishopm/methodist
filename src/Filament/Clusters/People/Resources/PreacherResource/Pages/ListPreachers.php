<?php

namespace Bishopm\Methodist\Filament\Clusters\People\Resources\PreacherResource\Pages;

use Bishopm\Methodist\Filament\Clusters\People\Resources\PreacherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPreachers extends ListRecords
{
    protected static string $resource = PreacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

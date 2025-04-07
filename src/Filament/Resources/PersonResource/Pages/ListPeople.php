<?php

namespace Bishopm\Methodist\Filament\Resources\PersonResource\Pages;

use Bishopm\Methodist\Filament\Resources\PersonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeople extends ListRecords
{
    protected static string $resource = PersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

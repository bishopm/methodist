<?php

namespace Bishopm\Methodist\Filament\Resources\MinisterResource\Pages;

use Bishopm\Methodist\Filament\Resources\MinisterResource;
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

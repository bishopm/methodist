<?php

namespace Bishopm\Methodist\Filament\Resources\SocietyResource\Pages;

use Bishopm\Methodist\Filament\Resources\SocietyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSocieties extends ListRecords
{
    protected static string $resource = SocietyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

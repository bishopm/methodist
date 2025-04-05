<?php

namespace Bishopm\Methodist\Filament\Clusters\Settings\Resources\MidweekResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Settings\Resources\MidweekResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMidweeks extends ListRecords
{
    protected static string $resource = MidweekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

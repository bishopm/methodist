<?php

namespace Bishopm\Methodist\Filament\Clusters\Settings\Resources\LectionResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Settings\Resources\LectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLections extends ListRecords
{
    protected static string $resource = LectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

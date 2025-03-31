<?php

namespace Bishopm\Methodist\Filament\Clusters\People\Resources\LeaderResource\Pages;

use Bishopm\Methodist\Filament\Clusters\People\Resources\LeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaders extends ListRecords
{
    protected static string $resource = LeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

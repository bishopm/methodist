<?php

namespace Bishopm\Methodist\Filament\Clusters\People\Resources\LeaderResource\Pages;

use Bishopm\Methodist\Filament\Clusters\People\Resources\LeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeader extends EditRecord
{
    protected static string $resource = LeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

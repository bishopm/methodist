<?php

namespace Bishopm\Methodist\Filament\Clusters\Settings\Resources\MidweekResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Settings\Resources\MidweekResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMidweek extends EditRecord
{
    protected static string $resource = MidweekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

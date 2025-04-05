<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources\MeetingResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Structures\Resources\MeetingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeeting extends EditRecord
{
    protected static string $resource = MeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

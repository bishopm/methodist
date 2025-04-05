<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources\MeetingResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Structures\Resources\MeetingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMeetings extends ListRecords
{
    protected static string $resource = MeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

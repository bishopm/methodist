<?php

namespace Bishopm\Methodist\Filament\Resources\PreacherResource\Pages;

use Bishopm\Methodist\Filament\Resources\PreacherResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPreacher extends EditRecord
{
    protected static string $resource = PreacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

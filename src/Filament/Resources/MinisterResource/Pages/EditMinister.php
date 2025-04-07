<?php

namespace Bishopm\Methodist\Filament\Resources\MinisterResource\Pages;

use Bishopm\Methodist\Filament\Resources\MinisterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMinister extends EditRecord
{
    protected static string $resource = MinisterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

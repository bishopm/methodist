<?php

namespace Bishopm\Methodist\Filament\Resources\SocietyResource\Pages;

use Bishopm\Methodist\Filament\Resources\SocietyResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditSociety extends EditRecord
{
    protected static string $resource = SocietyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Preaching plan')
                ->url(fn (): string => route('filament.admin.resources.circuits.plan', ['record' => $this->record->circuit_id, 'today' => date('Y-m-d')])),
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace Bishopm\Methodist\Filament\Resources\CircuitResource\Pages;

use Bishopm\Methodist\Filament\Resources\CircuitResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditCircuit extends EditRecord
{
    protected static string $resource = CircuitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Preaching plan')
                ->url(fn (): string => route('filament.admin.resources.circuits.plan', ['record' => $this->record, 'today' => date('Y-m-d')])),
            Actions\DeleteAction::make(),
        ];
    }
}

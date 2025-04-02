<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource;
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
                ->url(fn (): string => route('admin.plan.edit', ['id' => $this->record])),
            Actions\DeleteAction::make(),
        ];
    }
}

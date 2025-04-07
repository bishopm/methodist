<?php

namespace Bishopm\Methodist\Filament\Resources\DistrictResource\Pages;

use Bishopm\Methodist\Filament\Resources\DistrictResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewDistrict extends ViewRecord
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Preaching plan')
                ->url(fn (): string => route('filament.admin.resources.circuits.plan', ['record' => $this->record, 'today' => date('Y-m-d')])),
            Actions\EditAction::make(),
        ];
    }
}

<?php

namespace Bishopm\Methodist\Filament\Resources\PersonResource\Pages;

use Bishopm\Methodist\Filament\Resources\PersonResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePerson extends CreateRecord
{
    protected static string $resource = PersonResource::class;

    protected function afterCreate(): void {
        if (($this->data['status']=="deacon") or ($this->data['status']=="minister")){
            $this->record->minister()->create([
                'person_id' => $this->getRecord()->id,
                'status'=>ucfirst($this->data['status']),
                'active'=>1
            ]);
        } elseif ($this->data['status']=="preacher"){
            $this->record->preacher()->create([
                'person_id' => $this->getRecord()->id,
                'status'=>'preacher',
                'active'=>1
            ]);
        }
    }

}

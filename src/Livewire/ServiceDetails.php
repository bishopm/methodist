<?php

namespace Bishopm\Methodist\Livewire;

use Livewire\Component;

class ServiceDetails extends Component
{
    public array $service = [];

    /**
     * Mount the component with service data
     */
    public function mount(array $service)
    {
        $this->service = $service;
    }

    public function render()
    {
        return view('methodist::livewire.service-details');
    }
}

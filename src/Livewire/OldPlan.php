<?php
 
namespace Bishopm\Methodist\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Plan extends Component
{
    public $servicetypes = [];
    public $preachers = [];
    public $service_id;
    public $servicedate;
    public $servicetype;
    public $selectedPreacher;
    protected $listeners = ['refresh' => '$refresh'];

    public function mount($service_id, $servicedate, $currentPreacher = null, $preachers = [])
    {
        $this->service_id = $service_id;
        $this->servicedate = $servicedate;
        $this->selectedPreacher = $currentPreacher;
        $this->preachers = $preachers;
    }

    public function preacherChanged(){
        dd($this->selectedPreacher);
    }

    public function render()
    {
        return view('methodist::livewire.plan');
    }
}
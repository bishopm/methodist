<?php
 
namespace Bishopm\Methodist\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Plan extends Component
{
    public $week = [];
    public $servicetypes = [];
    public $preachers = [];
    public $service_id;
    public $servicedate;

    public function updatePlan($sdata, $sdate, $sid, $type){
        dd($sid);
    }

    public function render()
    {
        return view('methodist::livewire.plan');
    }
}
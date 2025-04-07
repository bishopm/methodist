<?php

namespace Bishopm\Methodist\Livewire;

use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Midweek;
use Livewire\Component;
use Bishopm\Methodist\Models\Plan;
use Bishopm\Methodist\Models\Society;
use Bishopm\Methodist\Models\Person;
use Bishopm\Methodist\Models\Service;

class PreachingPlan extends Component
{
    public $schedule = [];
    public $services = [];
    public $midweeks = [];
    public $dates = [];
    public $preachers = [];
    public $editingCell = null;
    public $selectedPreacherId = null;
    public $serviceids;
    public $selectedServiceType = null;
    public $circuit;
    public $period;
    public $firstday;
    public $today;
    
    // Service type options
    public $serviceTypes = [];
    public $authorisedServices = [];
    protected $listeners = ['clickedOutside' => 'saveAndClose'];

    public function mount($record, $today=null)
    {
        if (!$today){
            $today=date('Y-m-d');
        }
        $this->today=$today;;
        $this->circuit=Circuit::find($record);
        $this->serviceTypes=array_merge([''=>''],$this->circuit->servicetypes);
        ksort($this->serviceTypes);
        // Get all societies
        $societies = Society::withWhereHas('services')->where('circuit_id',$record)->orderBy('society')->get();
        foreach ($societies as $society){
            foreach ($society->services as $service){
                $this->services[$society->society][$service->servicetime]['id']=$service->id;
                $this->services[$society->society][$service->servicetime]['time']=$service->servicetime;
                $this->serviceids[]=$service->id;
            }
            ksort($this->services[$society->society]);
        }
        // Get all preachers
        $ministers = Person::where('circuit_id',$record)->whereHas('minister', function ($q){ $q->where('status','<>','Supernumerary Minister');})->orderBy('surname')->orderBy('firstname')->get();
        $supernumeraries = Person::where('circuit_id',$record)->whereHas('minister', function ($q){ $q->where('status','Supernumerary Minister');})->orderBy('surname')->orderBy('firstname')->get();
        $preachers = Person::where('circuit_id',$record)->whereHas('preacher', function ($q){ $q->where('active',1);})->orderBy('surname')->orderBy('firstname')->get();
        foreach ($ministers as $minister){
            $this->preachers[$minister->id] = ['name' => substr($minister->firstname,0,1) . " " . $minister->surname,'id' => $minister->id];
        }
        foreach ($supernumeraries as $super){
            $this->preachers[$super->id] = ['name' => substr($super->firstname,0,1) . " " . $super->surname,'id' => $super->id];
        }
        foreach ($preachers as $preacher){
            $this->preachers[$preacher->id] = ['name' => substr($preacher->firstname,0,1) . " " . $preacher->surname,'id' => $preacher->id];
        }
        
        // Generate the upcoming 13 Sundays
        $this->generateSundays();
        
        // Load the current schedule
        $this->loadSchedule();
        $this->authorisedServices = $this->getUserAuthorisedServices();
    }

    private function getUserAuthorisedServices()
    {
        $allsocieties=Society::where('circuit_id',$this->circuit->id)->get()->pluck('id');
        if (auth()->user()->hasRole('Super Admin')){
            return Service::whereIn('society_id',$allsocieties)->get()->pluck('id')->toArray();
        } else if (auth()->user()->circuits){
            if (in_array($this->circuit->id,auth()->user()->circuits)){
                return Service::whereIn('society_id',$allsocieties)->get()->pluck('id')->toArray();
            } else {
                return [];
            }
        } else if (auth()->user()->societies){
            return Service::whereIn('society_id',auth()->user()->societies)->get()->pluck('id')->toArray();
        } else {
            return [];
        }
    }
    
    public function generateSundays()
    {
        $thismonth=intval(date('n',strtotime($this->today)));
        $thisyear=intval(date('Y',strtotime($this->today)));
        $yy=$thisyear;
        if ($this->circuit->plan_month==3){
            $plans[0]=[3,4,5];
            $plans[1]=[6,7,8];
            $plans[2]=[9,10,11];
            $plans[3]=[12,1,2];
            if ($thismonth<3){
                $yy=$thisyear-1;
            }
        } elseif ($this->circuit->plan_month==2){
            $plans[0]=[2,3,4];
            $plans[1]=[5,6,7];
            $plans[2]=[8,9,10];
            $plans[3]=[11,12,1];
            if ($thismonth<2){
                $yy=$thisyear-1;
            }
        } else {
            $plans[0]=[1,2,3];
            $plans[1]=[4,5,6];
            $plans[2]=[7,8,9];
            $plans[3]=[10,11,12];
        }
        foreach ($plans as $kk=>$pp){
            if (in_array($thismonth,$pp)){
                $plan=$plans[$kk];
            }
        }
        if ($plan[0]<10){
            $firstday = $yy . '-0' . $plan[0] . '-01';
        } else {
            $firstday = $yy . '-' . $plan[0] . '-01';
        }
        $lastday=date('Y-m-d',strtotime($firstday . " + 3 months"));
        $dow=intval(date('N',strtotime($firstday)));
        if ($dow==7){
            $firstsunday=$firstday;
        } else {
            $firstsunday=date("Y-m-d",strtotime($firstday)+86400*(7-$dow));
        }
        $dates[]=$firstsunday;
        for ($w=1;$w<15;$w++){
            if (in_array(intval(date('n',strtotime($firstsunday)+86400*7*$w)),$plan)){
                $dates[$w]=date("Y-m-d",strtotime($firstsunday)+86400*7*$w);
            }
        }
        $this->midweeks=Midweek::where('servicedate','>=',$firstday)->where('servicedate','<',$lastday)->orderBy('servicedate','ASC')->get()->pluck('servicedate','midweek')->toArray();
        foreach ($this->midweeks as $desc=>$mw){
            if (in_array($desc,$this->circuit->midweeks)){
                $dates[]=$mw;
            }
        }
        sort($dates);
        $this->dates=$dates;
        $this->firstday=$firstday;
        $this->period = date("j F Y",strtotime($firstday)) . " - " . date("j F Y",strtotime($lastday . '- 1 day'));
    }
    
    public function loadSchedule()
    {
        // Prepare the schedule array
        $this->schedule = [];
        
        // Initialize with empty values
        foreach ($this->services as $society){
            foreach ($society as $service){
                $this->schedule[$service['id']] = [];
                foreach ($this->dates as $date) {
                    $this->schedule[$service['id']][$date] = null;
                }
            }
        }
        
        // Load actual schedule data
        $scheduleData = Plan::with('person')->whereIn('service_id', $this->serviceids)
            ->whereIn('servicedate', $this->dates)
            ->where('person_id','>',0)->orWhere('servicetype','<>','')
            ->with('person')
            ->get();
        
        foreach ($scheduleData as $item) {
            if ($item->person_id){
                $preachername=substr($item->person->firstname,0,1) . " " . $item->person->surname;
            } else {
                $preachername="";
            }
            $this->schedule[$item->service_id][$item->servicedate] = [
                'preacher_id' => $item->person_id,
                'preacher_name' => $preachername,
                'servicetype' => $item->servicetype ?? ''
            ];
        }
    }
    
    public function startEditing($service_id, $date)
    {
        // Check if user is authorized to edit this service
        if (!in_array($service_id, $this->authorisedServices)) {
            // Optional: Flash a message or log the attempt
            session()->flash('message', 'You do not have permission to edit this service.');
            return;
        }

        $this->editingCell = "$service_id-$date";
        $this->selectedPreacherId = $this->schedule[$service_id][$date]['preacher_id'] ?? null;
        $this->selectedServiceType = $this->schedule[$service_id][$date]['servicetype'] ?? '';

        // Dispatch browser event to set up outside click detection
        $this->dispatch('cell-editing-started', ['cellId' => $this->editingCell]);
    }
    
    public function updatedSelectedPreacherId()
    {
        if ($this->editingCell) {
            // Auto-save after selection change
            $this->saveChanges();
        }
    }
    
    public function updatedSelectedServiceType()
    {
        if ($this->editingCell) {
            // Auto-save after selection change
            $this->saveChanges();
        }
    }

    public function saveChanges()
    {
        if (!$this->editingCell) {
            return;
        }
        
        $service_id=substr($this->editingCell,0,strpos($this->editingCell,"-"));
        $date=substr($this->editingCell,1+strpos($this->editingCell,"-"));
        // Update the database
        Plan::updateOrCreate(
            [
                'service_id' => $service_id, 
                'servicedate' => $date,
                'person_id' => $this->selectedPreacherId,
                'servicetype' => $this->selectedServiceType
            ]
        );
        
        // Update the local data
        if ($this->selectedPreacherId) {
            $preacher = $this->preachers[$this->selectedPreacherId];
            $this->schedule[$service_id][$date] = [
                'preacher_id' => $this->selectedPreacherId,
                'preacher_name' => $preacher['name'] ?? 'Unknown',
                'servicetype' => $this->selectedServiceType
            ];
        } else {
            // If no preacher selected, set to null
            $this->schedule[$service_id][$date] = null;
        }
    }

    public function saveAndClose()
    {
        if ($this->editingCell) {
            $this->saveChanges();
            $this->editingCell = null;
            $this->selectedPreacherId = null;
            $this->selectedServiceType = null;
        }
    }
    
    public function render()
    {
        return view('methodist::livewire.preaching-plan');
    }    
}
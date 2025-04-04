<?php

namespace Bishopm\Methodist\Livewire;

use Livewire\Component;
use Bishopm\Methodist\Models\Plan;
use Bishopm\Methodist\Models\Society;
use Bishopm\Methodist\Models\Person;
use Carbon\Carbon;

class PreachingPlan extends Component
{
    public $schedule = [];
    public $societies = [];
    public $sundays = [];
    public $preachers = [];
    public $editingCell = null;
    public $selectedPreacherId = null;
    
    public function mount()
    {
        // Get all societies
        $this->societies = Society::orderBy('society')->get();
        
        // Get all preachers
        $this->preachers = Person::orderBy('surname')->get();
        
        // Generate the upcoming 13 Sundays
        $this->generateSundays();
        
        // Load the current schedule
        $this->loadSchedule();
    }
    
    public function generateSundays()
    {
        $date = Carbon::now()->startOfWeek()->next(Carbon::SUNDAY);
        $this->sundays = [];
        
        for ($i = 0; $i < 13; $i++) {
            $this->sundays[] = (clone $date)->addWeeks($i);
        }
    }
    
    public function loadSchedule()
    {
        // Prepare the schedule array
        $this->schedule = [];
        
        // Initialize with empty values
        foreach ($this->societies as $church) {
            $this->schedule[$church->id] = [];
            
            foreach ($this->sundays as $sunday) {
                $this->schedule[$church->id][$sunday->format('Y-m-d')] = null;
            }
        }
        
        // Load actual schedule data
        $scheduleData = Plan::whereIn('society_id', $this->societies->pluck('id'))
            ->whereIn('date', collect($this->sundays)->map->format('Y-m-d'))
            ->with('preacher')
            ->get();
        
        foreach ($scheduleData as $item) {
            $this->schedule[$item->church_id][$item->date] = [
                'preacher_id' => $item->preacher_id,
                'preacher_name' => $item->preacher->name ?? 'Unknown'
            ];
        }
    }
    
    public function startEditing($churchId, $date)
    {
        $this->editingCell = "$churchId-$date";
        $this->selectedPreacherId = $this->schedule[$churchId][$date]['preacher_id'] ?? null;
    }
    
    public function updateSchedule()
    {
        if (!$this->editingCell) {
            return;
        }
        
        [$churchId, $date] = explode('-', $this->editingCell);
        
        // Update the database
        Plan::updateOrCreate(
            ['church_id' => $churchId, 'date' => $date],
            ['preacher_id' => $this->selectedPreacherId]
        );
        
        // Update the local data
        if ($this->selectedPreacherId) {
            $preacher = $this->preachers->firstWhere('id', $this->selectedPreacherId);
            $this->schedule[$churchId][$date] = [
                'preacher_id' => $this->selectedPreacherId,
                'preacher_name' => $preacher ? $preacher->name : 'Unknown'
            ];
        } else {
            // If no preacher selected, set to null
            $this->schedule[$churchId][$date] = null;
        }
        
        // Close the editing cell
        $this->editingCell = null;
        $this->selectedPreacherId = null;
    }
    
    public function cancelEditing()
    {
        $this->editingCell = null;
        $this->selectedPreacherId = null;
    }
    
    public function render()
    {
        return view('methodist::livewire.preaching-plan');
    }
}
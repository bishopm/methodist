<div>
    <div 
        x-data="{
            setupOutsideClickListener(cellId) {
                // Wait for the next tick to avoid the click that opened the cell from closing it
                setTimeout(() => {
                    const handleClickOutside = (event) => {
                        // Check if the click is outside the editing area
                        const editingCell = document.querySelector(`[data-cell-id='${cellId}']`);
                        if (editingCell && !editingCell.contains(event.target)) {
                            // Dispatch the Livewire event
                            @this.call('clickedOutside');
                            // Remove the event listener
                            document.removeEventListener('click', handleClickOutside);
                        }
                    };
                    
                    // Add the event listener
                    document.addEventListener('click', handleClickOutside);
                }, 100);
            }
        }"
        @cell-editing-started.window="setupOutsideClickListener($event.detail.cellId)"
        class="overflow-x-auto"
    >
        <table class="table table-condensed table-striped">
            <thead>
                <tr>
                    <th>
                        <a class="text-black" style="text-decoration: none;" href="{{url('/admin/structures/circuits/' . $circuit->id . '/edit')}}" title="Back to circuit page"><i class="text-black bi bi-house"></i> Home</a>
                    </th>
                    <th class="text-center" colspan="100%">{{$circuit->circuit}} Circuit {{$circuit->reference}} Preaching plan ({{$period}})</th>
                </tr>
                <tr>
                    <th class="bg-dark text-white" colspan="2">
                        <a href="{{route('filament.admin.structures.resources.circuits.plan', ['record' => $circuit->id, 'today' => date('Y-m-d',strtotime($firstday . '- 3 months'))])}}"><i class="text-white bi bi-arrow-left h4"></i></a>
                        <a href="/plan/{{$circuit->id}}/{{$today}}" class="mx-3 btn btn-sm btn-secondary">View PDF</a>
                        <a href="{{route('filament.admin.structures.resources.circuits.plan', ['record' => $circuit->id, 'today' => date('Y-m-d',strtotime($firstday . '+ 3 months'))])}}"><i class="text-white bi bi-arrow-right h4"></i></a>
                    </th>
                    @foreach($dates as $date)
                        <th class="bg-dark text-white text-center">
                            {{ date('j M',strtotime($date)) }}
                            @php $mw=array_search($date,$midweeks); @endphp
                            <div class="text-sm text-center">{{$mw}}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($services as $society=>$times)
                    @foreach ($times as $time=>$service)
                        <tr>
                            <td class="border">{{ $society }}</td><td class="border font-medium">{{ $time }}</td>
                            @foreach($dates as $thisd)
                                @php $date = $thisd; @endphp
                                <td class="border" wire:key="cell-{{ $service['id'] }}-{{ $date }}">
                                    @if($editingCell === "{$service['id']}-{$date}")
                                        <div class="flex flex-col">
                                            <select wire:model="selectedServiceType" class="form-select form-select-sm">
                                                @foreach($serviceTypes as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <select wire:model="selectedPreacherId" class="form-select form-select-sm">
                                                <option value="">-- Select --</option>
                                                @foreach($preachers as $preacher)
                                                    <option value="{{ $preacher['id'] }}">{{ $preacher['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div 
                                            x-data="{}"
                                            @click="$wire.startEditing('{{ $service['id'] }}', '{{ $date }}')" 
                                            class="cursor-pointer hover:bg-gray-100 rounded text-center"
                                        >
                                        @if(isset($schedule[$service['id']][$date]))
                                            <div class="flex flex-col text-center">
                                                @if(!empty($schedule[$service['id']][$date]['servicetype']))
                                                    <span class="items-center text-xs font-medium">
                                                        {{ $schedule[$service['id']][$date]['servicetype'] }}
                                                    </span>
                                                @endif
                                                <span>{{ $schedule[$service['id']][$date]['preacher_name'] }}</span>
                                            </div>
                                        @else
                                            —
                                        @endif
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>    
    @foreach($serviceTypes as $key => $label)
        @if($key)
            <span class="badge bg-dark">{{ $key }} : {{ $label }}</span>
        @endif
    @endforeach
</div>
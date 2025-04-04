<div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="px-4 py-2 bg-gray-100 border">Church</th>
                    @foreach($sundays as $sunday)
                        <th class="px-4 py-2 bg-gray-100 border">
                            {{ $sunday->format('M j') }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($churches as $church)
                    <tr>
                        <td class="px-4 py-2 border font-medium">{{ $church->name }}</td>
                        
                        @foreach($sundays as $sunday)
                            @php $date = $sunday->format('Y-m-d'); @endphp
                            <td class="px-2 py-1 border" wire:key="cell-{{ $church->id }}-{{ $date }}">
                                @if($editingCell === "{$church->id}-{$date}")
                                    <div class="flex flex-col space-y-2">
                                        <select wire:model="selectedPreacherId" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="">-- Select --</option>
                                            @foreach($preachers as $preacher)
                                                <option value="{{ $preacher->id }}">{{ $preacher->name }}</option>
                                            @endforeach
                                        </select>
                                        
                                        <div class="flex space-x-2">
                                            <button wire:click="updateSchedule" class="inline-flex items-center px-2 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 disabled:opacity-25 transition">
                                                Save
                                            </button>
                                            <button wire:click="cancelEditing" class="inline-flex items-center px-2 py-1 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div 
                                        x-data="{}"
                                        @click="$wire.startEditing('{{ $church->id }}', '{{ $date }}')" 
                                        class="p-2 min-h-[40px] cursor-pointer hover:bg-gray-100 rounded"
                                    >
                                        {{ $schedule[$church->id][$date]['preacher_name'] ?? '—' }}
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="max-w-3xl mx-auto p-4 bg-white">
    <h2 class="text-xl font-bold mb-2">
        {{ $service['liturgical_day'] ?? 'Unnamed Service' }}
    </h2>

    <p class="text-sm text-gray-600 mb-4">
        Service Date: {{ \Carbon\Carbon::parse($service['sunday'])->toFormattedDateString() }}
    </p>

    {{-- Sunday Readings --}}
    @if(!empty($service['sunday_readings']))
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Sunday Readings</h3>
            <ul class="list-disc pl-6">
                @foreach ($service['sunday_readings'] as $key => $reading)
                    @if (str_contains($reading, ' or '))
                        @php
                            $readings = explode(' or ', $reading);
                        @endphp
                        <li><strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong>
                            @foreach($readings as $ndx=>$subReading)
                                <a target="_blank" href="https://www.biblegateway.com/passage/?search={{ $subReading }}">{{ $subReading }}</a>
                                @if ($ndx<count($readings)-1)
                                    or
                                @endif
                            @endforeach
                        </li>
                    @else
                        <li><strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> 
                            <a target="_blank" href="https://www.biblegateway.com/passage/?search={{ $reading }}">{{ $reading }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Midweek Readings --}}
    @if(!empty($service['midweek_readings']))
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Midweek Readings</h3>
            <ul class="list-disc pl-6">
                @foreach($service['midweek_readings'] as $key => $reading)
                    <li><strong>{{ ucfirst($key) }}:</strong> {{ $reading }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<x-methodist::layouts.web pageName="Home">
    <h1>{{$circuit->circuit}} {{$circuit->reference}}</h1>
    <h5><a href="{{url('/') . '/' . $circuit->district->slug}}">{{$circuit->district->district}} District</a></h5>
    <div style="height:400px" id="map"></div>
    <script>
        var map = L.map('map');
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        @foreach ($circuit->societies as $soc)
            var marker = L.marker([{{$soc->latitude}}, {{$soc->longitude}}]).bindPopup('<a href="{{url()->current() . '/' . $soc->id}}">{{$soc->society}}</a>').addTo(map);
        @endforeach
        var markers = [
        @foreach ($circuit->societies as $soc)
             [{{$soc->latitude}}, {{$soc->longitude}}],
        @endforeach
        ];
        var bounds = new L.LatLngBounds(markers);
        map.fitBounds(bounds, {padding: [25,25]});
    </script>
    <ul class="list-unstyled">
        <h3>Societies</h3>
        @foreach ($circuit->societies as $society)
            <li><a href="{{url('/' . $circuit->district->slug . '/' . $circuit->slug . '/' . $society->id)}}">{{$society->society}}</a></li>
        @endforeach
    </ul>
    <ul class="list-unstyled">
        <h3>Clergy</h3>
        @foreach ($circuit->persons as $person)
            @if (in_array('Minister',json_decode($person->pivot->status)))
                <li>{{$person->title}} {{$person->firstname}} {{$person->surname}} 
                @if ($person->minister->leadership)
                    @foreach ($person->minister->leadership as $lead)    
                         <span class="bg-dark badge text-white mx-2 py-1 px-1">{{$lead}}</span>
                    @endforeach
                @endif
                </li>
            @endif
        @endforeach
    </ul>
</x-methodist::layouts.web>


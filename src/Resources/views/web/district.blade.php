<x-methodist::layouts.web pageName="Home">
    <h1>MCSA districts</h1>
    <h5><a href="{{url('/')}}">All districts</a></h5>
    <div style="height:400px" id="map"></div>
    <script>
        var map = L.map('map');
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        @foreach ($district->circuits as $circuit)
            @foreach ($circuit->societies as $soc)
                var marker = L.marker([{{$soc->latitude}}, {{$soc->longitude}}]).bindPopup('<a href="{{url()->current() . '/' . $soc->circuit->slug . '/' . $soc->id}}">{{$soc->society}}</a>').addTo(map);
            @endforeach
        @endforeach
        var markers = [
        @foreach ($district->circuits as $circ)
            @foreach ($circ->societies as $soc)
                [{{$soc->latitude}}, {{$soc->longitude}}],
            @endforeach
        @endforeach
        ];
        var bounds = new L.LatLngBounds(markers);
        map.fitBounds(bounds, {padding: [25,25]});
    </script>
    <ul class="list-unstyled">
        @foreach ($district->circuits->sortBy('reference') as $circuit)
            <li><a href="{{url('/' . $district->slug . '/' . $circuit->slug)}}">{{$circuit->reference}} {{$circuit->circuit}}</a></li>
        @endforeach
    </ul>
</x-methodist::layouts.web>
<x-methodist::layouts.web pageName="Home">
    <h1>MCSA districts</h1>
    <h5><a href="{{url('/')}}">All districts</a></h5>
    <div style="height:400px" id="map"></div>
    <script>
        var map = L.map('map');
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: 'pk.eyJ1IjoiYmlzaG9wbSIsImEiOiJjanNjenJ3MHMwcWRyM3lsbmdoaDU3ejI5In0.M1x6KVBqYxC2ro36_Ipz_w'
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
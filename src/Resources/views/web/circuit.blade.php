<x-methodist::layouts.web pageName="Home">
    <h1><a href="{{url('/')}}"><i class="bi bi-house mx-2"></i></a>{{$circuit->circuit}} {{$circuit->reference}}</h1>
    <h5><a href="{{url('/') . '/' . $circuit->district->slug}}">{{$circuit->district->district}} District</a></h5>
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
    <div class="row mt-3">
        <div class="col-6">
            <h3><a target="_blank" href="{{url('/') . '/plan/' . $circuit->slug . '/' . date('Y-m-d') }}">Preaching plan</a></h3>
            <ul class="list-unstyled">
                <h3>Societies</h3>
                @foreach ($circuit->societies as $society)
                    <li><a href="{{url('/' . $circuit->district->slug . '/' . $circuit->slug . '/' . $society->id)}}">{{$society->society}}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="col-6">
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
            <h3>Lectionary readings</h3>
            <h5>{{date('l, j F Y',strtotime($lect->servicedate))}}</h5>
            @foreach ($lect->readings as $service)
                <h5>{{$service['name']}}</h5>
                <ul class="list-unstyled">
                    @foreach ($service['readings'] as $reading)
                        @php
                            if (str_contains($reading,' or ')){
                                $allreadings=explode(' or ',$reading);
                                print "<li>";
                                foreach ($allreadings as $ndx=>$thisreading){
                                    print "<a target=\"_blank\" href=\"https://www.biblegateway.com/passage/?search=" . $thisreading . "\">" . $thisreading . "</a>";
                                    if ($ndx<count($allreadings)-1){
                                        print " or ";
                                    }
                                }
                                print "</li>";
                            } else {
                                print "<li><a target=\"_blank\" href=\"https://www.biblegateway.com/passage/?search=" . $reading . "\">" . $reading . "</a></li>";
                            }
                        @endphp
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>
</x-methodist::layouts.web>


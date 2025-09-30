<x-methodist::layouts.web pageName="Circuit">
    <h1 class="text-md-start text-center">
        <a href="{{url('/')}}"><img src="{{ asset('methodist/images/mcsa.png') }}" alt="MCSA Logo" style="max-height:30px; margin-bottom:5px;margin-right:5px;"></a>{{$circuit->circuit}} {{$circuit->reference}}
    </h1>
    <h5 class="text-md-start text-center"><a href="{{url('/') . '/' . $circuit->district->slug}}">{{$circuit->district->district}} District</a></h5>
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
    <div class="row">
        <div class="col-md-4 pt-3">
            <h3 class="text-md-start text-center"><a target="_blank" href="{{url('/') . '/plan/' . $circuit->slug . '/' . date('Y-m-d') }}">Preaching plan</a></h3>
            <ul class="list-unstyled">
                <h3 class="text-md-start text-center">Societies</h3>
                @foreach ($circuit->societies->sortBy('society') as $society)
                    <li><a href="{{url('/' . $circuit->district->slug . '/' . $circuit->slug . '/' . $society->id)}}">{{$society->society}}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-4 pt-3">
            <div class="row">
                <h3 class="text-md-start text-center">Ministers</h3>
                @foreach ($ministers as $minister)
                    @if ((in_array('Minister',json_decode($minister->pivot->status))) or (in_array('Superintendent',json_decode($minister->pivot->status))))
                        <div class="rounded col text-small text-md-start text-center">
                            <a href="{{url('/ministers/' . $minister->id)}}">
                                @if ($minister->image)
                                    <img class="rounded" width="100px" src="{{url('/storage/public/' . $minister->image)}}">
                                @else 
                                    <img class="rounded" width="100px" src="{{url('/methodist/images/blank.png')}}">
                                @endif
                                <br>
                                <small>{{$minister->firstname}} {{$minister->surname}}
                                    @if (in_array('Superintendent',json_decode($minister->pivot->status)))
                                        <br><span class="bg-dark badge text-white text-small">Superintendent</span>
                                    @endif
                                </small>
                            </a>
                        </div>
                    @endif
                @endforeach
                <table>
                @foreach ($leaders as $category=>$persons)
                    @if ($category <> "Guest")
                        <tr class="bg-primary">
                            <th class="text-white text-center">{{$category}}@if(count($persons)>1)s @endif</th>
                        </tr>
                        <tr>
                            <td>
                            @foreach ($persons as $person)
                                {{$person->title}} {{substr($person->firstname,0,1)}}. {{$person->surname}}@if(!$loop->last), @else.@endif
                            @endforeach
                            </td>
                        </tr>
                    @endif
                @endforeach
                </table>
            </div>
        </div>
        <div class="col-md-4 pt-3">
            <h3 class="text-md-start text-center">Lectionary readings</h3>
            <livewire:service-details :service="$lects" />
        </div>
    </div>
</x-methodist::layouts.web>


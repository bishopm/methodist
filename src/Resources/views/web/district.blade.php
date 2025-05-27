<x-methodist::layouts.web pageName="Home">
    <h1><a href="{{url('/')}}"><i class="bi bi-house mx-2"></i></a>{{$district->district}} District</h1>
    <h5><a href="{{url('/')}}">All districts</a></h5>
    <div class="row mt-3">
        <div class="col-md-3">
            @if ($bishop and $bishop->minister->image)
                <a href="{{url('/ministers/' . $bishop->id)}}">
                    <img width="100px" src="{{url('/storage/public/' . $bishop->minister->image)}}">
                </a>
            @elseif ($bishop)
                <a href="{{url('/ministers/' . $bishop->id)}}">
                    <img width="100px"  src="{{url('/methodist/images/blank.png')}}">
                </a>
            @endif
            <h6 class="mt-3"><span class="bg-dark badge text-white text-small">District Bishop</span> {{$bishop->name ?? ''}}</h6>
            <span class="bg-dark badge text-white text-small">District Office</span>
            {!!$district->contact!!}
        </div>
        <div class="col-md-9">
            <div style="height:400px" id="map" class="mb-3"></div>
            <script>
                var map = L.map('map').setView([{{$district->latitude}}, {{$district->longitude}}], 15);
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    maxZoom: 18,
                    id: 'mapbox/streets-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: 'pk.eyJ1IjoiYmlzaG9wbSIsImEiOiJjanNjenJ3MHMwcWRyM3lsbmdoaDU3ejI5In0.M1x6KVBqYxC2ro36_Ipz_w'
                }).addTo(map);
                var marker = L.marker([{{$district->latitude}}, {{$district->longitude}}]).bindPopup('District Office').addTo(map);
            </script>        
        </div>
        <div class="col-md-4">
            <h3>Circuits</h3>
            <ul class="list-unstyled">
                @foreach ($district->circuits->sortBy('reference') as $circuit)
                    @if ($circuit->active)
                        <li><a href="{{url('/' . $district->slug . '/' . $circuit->slug)}}">{{$circuit->reference}} {{$circuit->circuit}}</a></li>
                    @else
                        <li>{{$circuit->reference}} {{$circuit->circuit}}</li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div class="col-md-8">
            <h3>Ministers</h3>
            <div class="row">
                @foreach ($ministers as $minister)
                    @if ($minister->minister->active)
                        <div class="col text-center">
                            <a href="{{url('/ministers/' . $minister->id)}}">
                                @if ($minister->minister->image)
                                    <img width="100px" src="{{url('/storage/public/' . $minister->minister->image)}}">
                                @else 
                                    <img width="100px" src="{{url('/methodist/images/blank.png')}}">
                                @endif
                                <br>
                                <small>{{$minister->firstname}} {{$minister->surname}}</small>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</x-methodist::layouts.web>
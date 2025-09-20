<x-methodist::layouts.web pageName="Society">
    <h1 class="text-md-start text-center"><a href="{{url('/')}}"><img src="{{ asset('methodist/images/mcsa.png') }}" alt="MCSA Logo" style="max-height:30px; margin-bottom:5px;margin-right:5px;"></a>{{$society->society}}</h1>
    <h5 class="text-md-start text-center"><a href="{{url('/') . '/' . $society->circuit->district->slug . '/' . $society->circuit->slug}}">{{$society->circuit->circuit}} Circuit {{$society->circuit->reference}}</a></h5>
    <div style="height:400px" id="map"></div>
    <script>
        var map = L.map('map').setView([{{$society->latitude}}, {{$society->longitude}}], 15);
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: 'pk.eyJ1IjoiYmlzaG9wbSIsImEiOiJjanNjenJ3MHMwcWRyM3lsbmdoaDU3ejI5In0.M1x6KVBqYxC2ro36_Ipz_w'
        }).addTo(map);
        var marker = L.marker([{{$society->latitude}}, {{$society->longitude}}]).addTo(map);
    </script>
    <table class="table">
        <tr>
            <th class="bg-dark text-white">Address</th><td>{{$society->address}}</td>
        </tr>
        <tr>
            <th class="bg-dark text-white">Website</th><td><a href="{{$society->website}}" target="_blank">{{$society->website}}</a></td>
        </tr>
        <tr>
            <th class="bg-dark text-white">Services</th><td>
                @foreach ($society->services as $service)
                    <span class="bg-dark badge text-white mx-1 py-1">{{$service->servicetime}}</span>
                @endforeach
            </td>
        </tr>
        <tr>
            <th class="bg-dark text-white">Preachers</th>
            <td>
                @foreach ($society->preachers->sortBy('person.surname') as $preacher)
                   {{$preacher->person->title}} {{substr($preacher->person->firstname,0,1)}} {{$preacher->person->surname}}@if(!$loop->last), @else.@endif
                @endforeach
            </td>
        </tr>       
        <tr>
            @if (count($ministers)>1)
                <th class="bg-dark text-white">Ministers</th>
            @else 
                <th class="bg-dark text-white">Minister</th>
            @endif
            <td>
                @foreach ($ministers as $minister)
                    {{$minister->title}} {{substr($minister->firstname,0,1)}} {{$minister->surname}}@if(!$loop->last), @else.@endif
                @endforeach
            </td>
        </tr>       
    </table>
</x-methodist::layouts.web>
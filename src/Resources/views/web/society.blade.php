<x-methodist::layouts.web pageName="Home">
    <h1>{{$society->society}}</h1>
    <h5><a href="{{url('/') . '/' . $society->circuit->district->slug . '/' . $society->circuit->slug}}">{{$society->circuit->circuit}} Circuit {{$society->circuit->reference}}</a></h5>
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
            <th>Address</th><td>{{$society->address}}</td>
        </tr>
        <tr>
            <th>Website</th><td>{{$society->website}}</td>
        </tr>
        <tr>
            <th>Services</th><td>
                @foreach ($society->services as $service)
                    {{$service->servicetime}} 
                @endforeach
            </td>
        </tr>
    </table>
</x-methodist::layouts.web>
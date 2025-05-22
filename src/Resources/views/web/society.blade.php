<x-methodist::layouts.web pageName="Home">
    <h1>{{$society->society}}</h1>
    <h5><a href="{{url('/') . '/' . $society->circuit->district->slug . '/' . $society->circuit->slug}}">{{$society->circuit->circuit}} Circuit {{$society->circuit->reference}}</a></h5>
    <div style="height:400px" id="map"></div>
    <script>
        var map = L.map('map').setView([{{$society->latitude}}, {{$society->longitude}}], 15);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        var marker = L.marker([{{$society->latitude}}, {{$society->longitude}}]).addTo(map);
    </script>
    {{$society}}
</x-methodist::layouts.web>
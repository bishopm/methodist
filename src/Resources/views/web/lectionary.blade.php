<x-methodist::layouts.web pageName="Home">
    <h1>Lectionary readings</h1>
    <h3>{{date('l, j F Y',strtotime($lect->sunday->date))}}</h3>
    @foreach ($lect->sunday->services as $service)
        <h5>{{$service->name}}</h5>
        <ul class="list-unstyled">
            @foreach ($service->readings as $reading)
                <li>{{$reading}}</li>
            @endforeach
        </ul>
    @endforeach
</x-methodist::layouts.web>
<x-methodist::layouts.web pageName="Home">
    <h1>Lectionary readings</h1>
    <h3>{{date('l, j F Y',strtotime($lect->servicedate))}}</h3>
    @foreach ($lect->readings as $service)
        <h5>{{$service['name']}}</h5>
        <ul class="list-unstyled">
            @foreach ($service['readings'] as $reading)
                <li><a target=_blank" href="https://www.biblegateway.com/passage/?search={{$reading}}">{{$reading}}</a></li>
            @endforeach
        </ul>
    @endforeach
</x-methodist::layouts.web>
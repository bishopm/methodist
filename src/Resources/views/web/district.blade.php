<x-methodist::layouts.web pageName="Home">
    <h1>MCSA districts</h1>
    <ul class="list-unstyled">
        @foreach ($district->circuits->sortBy('reference') as $circuit)
            <li><a href="{{url('/' . $district->slug . '/' . $circuit->slug)}}">{{$circuit->reference}} {{$circuit->circuit}}</a></li>
        @endforeach
    </ul>
</x-methodist::layouts.web>
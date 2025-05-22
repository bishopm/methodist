<x-methodist::layouts.web pageName="Home">
    <h1>MCSA districts</h1>
    <ul class="list-unstyled">
        @foreach ($districts as $district)
            <li><a href="{{url('/' . $district->slug)}}">{{$district->district}}</a></li>
        @endforeach
    </ul>
</x-methodist::layouts.web>
<x-methodist::layouts.web pageName="Home">
    <h1 class="text-md-start text-center">
        <a href="{{url('/')}}"><img src="{{ asset('methodist/images/mcsa.png') }}" alt="MCSA Logo" style="max-height:30px; margin-bottom:5px;margin-right:5px;"></a>MCSA
    </h1>
    <div class="row">
        <div class="col-md-6">
            <h3 class="text-md-start text-center">Districts</h3>
            <ul class="list-unstyled">
                @foreach ($districts as $district)
                    @if ($district->active)
                        <li><a href="{{url('/' . $district->slug)}}">{{$district->district}}</a></li>
                    @else
                        <li>{{$district->district}}</li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div class="col-md-6">
            <h3 class="text-md-start text-center">Lectionary readings</h3>
            <livewire:service-details :service="$lects" />
        </div>
    </div>
</x-methodist::layouts.web>
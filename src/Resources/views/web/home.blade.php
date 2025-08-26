<x-methodist::layouts.web pageName="Home">
    <h1 class="text-md-start text-center">MCSA</h1>
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
            @forelse ($lects as $lect)
                @foreach ($lect->readings as $service)
                    <h5>{{date('l, j F Y',strtotime($lect->servicedate))}}</h5>
                    <p>{{$service['name']}}</p>
                    <ul class="list-unstyled">
                        @foreach ($service['readings'] as $reading)
                            @php
                                if (str_contains($reading,' or ')){
                                    $allreadings=explode(' or ',$reading);
                                    print "<li>";
                                    foreach ($allreadings as $ndx=>$thisreading){
                                        print "<a target=\"_blank\" href=\"https://www.biblegateway.com/passage/?search=" . $thisreading . "\">" . $thisreading . "</a>";
                                        if ($ndx<count($allreadings)-1){
                                            print " or ";
                                        }
                                    }
                                    print "</li>";
                                } else {
                                    print "<li><a target=\"_blank\" href=\"https://www.biblegateway.com/passage/?search=" . $reading . "\">" . $reading . "</a></li>";
                                }
                            @endphp
                        @endforeach
                    </ul>
                @endforeach
            @empty
                No readings
            @endforelse
        </div>
    </div>
</x-methodist::layouts.web>
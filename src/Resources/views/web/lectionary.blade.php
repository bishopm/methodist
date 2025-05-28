<x-methodist::layouts.web pageName="Home">
    <h3>Lectionary readings</h3>
            @forelse ($lects as $lect)
                @foreach ($lect->readings as $service)
                    <h5>{{date('l, j F Y',strtotime($lect->servicedate))}}</h5>
                    <h5>{{$service['name']}}</h5>
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
</x-methodist::layouts.web>
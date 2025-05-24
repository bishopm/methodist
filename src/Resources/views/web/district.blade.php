<x-methodist::layouts.web pageName="Home">
    <h1><a href="{{url('/')}}"><i class="bi bi-house mx-2"></i></a>{{$district->district}} District</h1>
    <h5><a href="{{url('/')}}">All districts</a></h5>
    <h3>Ministers</h3>
    <div class="row">
        @foreach ($ministers as $minister)
            <div class="col-1 text-center">
                @if ($minister->minister->image)
                    <img class="img-fluid" src="{{url('/storage/public/' . $minister->minister->image)}}">
                @else 
                    <img class="img-fluid" src="{{url('/methodist/images/blank.png')}}">
                @endif
                <small>{{$minister->firstname}} {{$minister->surname}}</small>
            </div>
        @endforeach
    </div>
    <div class="row mt-3">
        <div class="col-6">
            <h3>Circuits</h3>
            <ul class="list-unstyled">
                @foreach ($district->circuits->sortBy('reference') as $circuit)
                    <li><a href="{{url('/' . $district->slug . '/' . $circuit->slug)}}">{{$circuit->reference}} {{$circuit->circuit}}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="col-6">
            <h3>Lectionary readings</h3>
            <h5>{{date('l, j F Y',strtotime($lect->servicedate))}}</h5>
            @foreach ($lect->readings as $service)
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
        </div>
    </div>
</x-methodist::layouts.web>
<x-methodist::layouts.web pageName="{{$minister->title}} {{$minister->firstname}} {{$minister->surname}}">
    <h4 class="text-md-start text-center"><a href="{{url('/')}}"><img src="{{ asset('methodist/images/mcsa.png') }}" alt="MCSA Logo" style="max-height:30px; margin-bottom:5px;margin-right:5px;"></a>{{$minister->title}} {{$minister->firstname}} {{$minister->surname}}</h4>
    @if ($minister->image)
        <img class="rounded" width="100px" src="{{url('/storage/public/' . $minister->image)}}">
    @else 
        <img class="rounded" width="100px" src="{{url('/methodist/images/blank.png')}}">
    @endif
    @if ($minister->minister->ordained)
        <p><b>Ordained:</b> {{$minister->minister->ordained}}</p>
    @endif
    @if ($minister->minister->leadership)
        @foreach ($minister->minister->leadership as $lead)
            <span class="bg-dark badge text-white text-small mx-3">{{$lead}}</span>
        @endforeach
    @endif
        
    @foreach ($minister->circuitroles as $circuit)
        @if (in_array('Minister',$circuit->status) or in_array('Superintendent',$circuit->status))
            <p>
                <a href="{{url('/' . $circuit->circuit->district->slug . '/' . $circuit->circuit->slug)}}">{{$circuit->circuit->circuit}} {{$circuit->circuit->reference}}</a>
                @if (count($societies))
                    ({{implode(", ",$societies[$circuit->circuit_id])}})
                @endif
            </p>
        @endif
    @endforeach
</x-methodist::layouts.web>
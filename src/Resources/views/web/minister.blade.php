<x-methodist::layouts.web pageName="{{$minister->title}} {{$minister->firstname}} {{$minister->surname}}">
    <h4><a href="{{url('/')}}"><i class="bi bi-house mx-2"></i></a>{{$minister->title}} {{$minister->firstname}} {{$minister->surname}}</h4>
    @if ($minister->image)
        <img class="rounded" width="100px" src="{{url('/storage/public/' . $minister->image)}}">
    @else 
        <img class="rounded" width="100px" src="{{url('/methodist/images/blank.png')}}">
    @endif
    @if ($minister->minister->ordained)
        <p><b>Ordained:</b> {{$minister->minister->ordained}}</p>
    @endif
    @foreach ($minister->minister->leadership as $lead)
        <span class="bg-dark badge text-white text-small mx-3">{{$lead}}</span>
    @endforeach
        
    @foreach ($minister->circuitroles as $circuit)
        @if (in_array('Minister',$circuit->status) or in_array('Superintendent',$circuit->status))
            <p><a href="{{url('/' . $circuit->circuit->district->slug . '/' . $circuit->circuit->slug)}}">{{$circuit->circuit->circuit}} {{$circuit->circuit->reference}}</a></p>
        @endif
    @endforeach
</x-methodist::layouts.web>
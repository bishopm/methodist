<div>
    <td class="text-center">
        <select class="form-select form-select-sm">
            <option></option>
            @foreach ($servicetypes as $type)
                @if ($week['servicetype']==$type)
                    <option selected>{{$type}}</option>
                @else
                    <option>{{$type}}</option>
                @endif
            @endforeach
        </select>
        <select class="form-select form-select-sm" wire:change="updatePlan($event.target.value, {{$servicedate}}, {{$week['service_id']}}, 'preacher')">
            <option></option>
            @foreach ($preachers as $preacher)
                @if ($week['preacher']==$preacher['id'])
                    <option selected value="{{$preacher['id']}}">{{$preacher['name']}}</option>
                @else 
                    <option value="{{$preacher['id']}}">{{$preacher['name']}}</option>
                @endif
            @endforeach
        </select>
    </td>
</div>
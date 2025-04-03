<div>
    <td class="text-center">
        <select class="form-select form-select-sm">
            <option></option>
            @foreach ($servicetypes as $type)
                @if ($servicetype==$type)
                    <option selected>{{$type}}</option>
                @else
                    <option>{{$type}}</option>
                @endif
            @endforeach
        </select>
        <select class="form-select form-select-sm" wire:model="selectedPreacher" wire:changed="preacherChanged">
            <option></option>
            @foreach ($preachers as $preacher)
                @if ($selectedPreacher==$preacher['id'])
                    <option selected value="{{$preacher['id']}}">{{$preacher['name']}}</option>
                @else 
                    <option value="{{$preacher['id']}}">{{$preacher['name']}}</option>
                @endif
            @endforeach
        </select>
    </td>
</div>
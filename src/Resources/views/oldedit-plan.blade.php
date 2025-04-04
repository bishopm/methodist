<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preaching Plan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <style>
        .form-select-sm {
            padding:0;
        }
        .table>:not(caption)>*>* {
            padding:2;
            vertical-align: middle;
        }
    </style>
    @filamentStyles
</head>
<body class="bg-gray-100">
    <div class="bg-dark py-2 text-center">
        <a class="text-white" style="text-decoration: none;" href="{{url('/admin')}}" title="Return to home page">
            <i class="bi bi-house"></i> Return to home
        </a>
    </div>
    <table class="table table-condensed">
        <tr>
            <th colspan="2"></th>
            @foreach ($dates as $day)
                <th class="text-center" style="position: sticky; top: 0; z-index: 1;">{{date('j M Y',strtotime($day))}}</th>
            @endforeach
        </tr>
        @foreach ($rows as $society=>$service)
            @foreach ($service as $ttt=>$row)
                <tr>
                    
                        <th>{{$society}}</th><td><small>{{$ttt}}</small></td>
                    @foreach ($row as $key=>$week)
                        @livewire('plan',[
                            'preachers'=>$preachers,
                            'servicetypes'=>$servicetypes, 
                            'currentPreacher'=>$week['preacher'], 
                            'servicetype'=>$week['servicetype'], 
                            'servicedate'=>$key,
                            'service_id'=>$week['service_id']
                        ]
                        , key("service-{$week['service_id']}-date-{$key}"))
                    @endforeach
                </tr>      
            @endforeach
        @endforeach
    </table>
    <script defer src="https://cdn.jsdelivr.net/npm/@imacrayon/alpine-ajax@0.12.0/dist/cdn.min.js"></script>
    @filamentScripts
</body>
</html>
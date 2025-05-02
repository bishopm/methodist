<?php

namespace Bishopm\Methodist\Filament\Widgets;

use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Society;
use Illuminate\Support\Facades\Auth;
use Webbingbrasil\FilamentMaps\Actions;
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;

class Map extends MapWidget
{
    protected int | string | array $columnSpan = 2;
    
    protected bool $hasBorder = false;

    public array $markers, $bounds;

    public array $mapOptions;

    public function mount(){
        $this->setUp();
    }

    protected string | array $tileLayerUrl = [
        'OpenStreetMap' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'Mapbox' => 'https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=',
    ];
     
    protected array $tileLayerOptions = [
        'OpenStreetMap' => [
            'attribution' => 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
        ],
        'Mapbox' => [
            'attribution' => 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        ],
    ];

    public function getMarkers(): array
    {
        $user=Auth::user();
        $this->markers=array();
        if (!$user->hasRole('Super Admin')){
            if ($user->districts){
                $circuits=Circuit::whereIn('district_id',$user->districts)->select('id')->get()->pluck('id');
                $societies=Society::whereIn('circuit_id',$circuits)->select('id','latitude','longitude','society')->get();
            } else if ($user->circuits){
                $societies=Society::whereIn('circuit_id',$user->circuits)->select('id','latitude','longitude','society')->get();
            } else if ($user->societies) {
                $societies=Society::whereIn('id',$user->societies)->select('id','latitude','longitude','society')->get();
            }
        } else {
            $societies=Society::select('id','latitude','longitude','society')->get();
        }
        foreach ($societies as $soc){
            if (($soc->latitude) and ($soc->longitude)){
                $this->markers[]=Marker::make($soc->id)->lat($soc->latitude)->lng($soc->longitude)->popup("<a href=\"http://methodist.local/admin/societies/" . $soc->id . "\">" . $soc->society . "</a>");
                $this->bounds[]=[$soc->latitude,$soc->longitude];
            }
        }
        $this->mapOptions = [
            'center' => [$soc->latitude,$soc->longitude],
            'zoom' => 11
        ];        
        return $this->markers;
    }

    public function getActions(): array
    {
        return [
        ];
    }

    public function setUp(): void
    {
        $this->getMarkers();
        $this
            ->height('500px')
            ->tileLayerUrl($this->tileLayerUrl['Mapbox'] . setting('general.mapbox_token'))
            ->mapOptions($this->mapOptions)
            ->mapMarkers($this->markers)
            ->fitBounds($this->bounds);
    }
}

<?php

namespace Bishopm\Methodist\Filament\Widgets;

use Bishopm\Methodist\Models\Society;
use Illuminate\Support\Facades\Auth;
use Webbingbrasil\FilamentMaps\Actions;
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;

class Map extends MapWidget
{
    protected int | string | array $columnSpan = 2;
    
    protected bool $hasBorder = false;

    public array $markers;

    public array $mapOptions;

    protected string | array $tileLayerUrl = [
        'OpenStreetMap' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'Mapbox' => 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}.png'
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
        $this->tileLayerUrl['Mapbox']='https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' . setting('general.mapbox_token') . '.png';
        $user=Auth::user();
        if (!$user->hasRole('Super Admin')){
            if ($user->circuits){
                $societies=Society::whereIn('circuit_id',$user->circuits)->select('id','latitude','longitude','society')->get();
            } else if ($user->societies) {
                $societies=Society::whereIn('id',$user->societies)->select('id','latitude','longitude','society')->get();
            }
            $this->markers=array();
            foreach ($societies as $soc){
                $this->markers[]=Marker::make($soc->id)->lat($soc->latitude)->lng($soc->longitude)->popup($soc->society);
            }
            $this->mapOptions = [
                'center' => [$soc->latitude,$soc->longitude],
                'zoom' => 11
            ];
            // Better would be bounds: https://stackoverflow.com/questions/17277686/leaflet-js-center-the-map-on-a-group-of-markers
        }
        return $this->markers;
    }

    public function getActions(): array
    {
        return [
        ];
    }
}

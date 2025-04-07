<?php

namespace Bishopm\Methodist\Filament\Widgets;

use Filament\Widgets\Widget;

class Map extends Widget
{
    protected static string $view = 'methodist::widgets.map';

    public ?array $widgetdata;

    function mount() {
        $this->widgetdata['map']="Map";
    }

    public static function canView(): bool 
    { 
        $roles =auth()->user()->roles->toArray(); 
        $permitted = array('Super Admin');
        foreach ($roles as $role){
            if ((in_array($role['name'],$permitted)) or (auth()->user()->isSuperAdmin())){
                return true;
            }
        }
        return false;
    }
}

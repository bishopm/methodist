<?php

namespace Bishopm\Methodist\Filament\Widgets;

use Filament\Widgets\Widget;

class Map extends Widget
{
    protected static string $view = 'methodist::widgets.map';

    public ?array $widgetdata;

    protected int | string | array $columnSpan = 'full';

    function mount() {
        $this->widgetdata['map']="Map";
    }

    public static function canView(): bool 
    { 
        return true;
    }
}

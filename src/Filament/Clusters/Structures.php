<?php

namespace Bishopm\Methodist\Filament\Clusters;

use Filament\Clusters\Cluster;

class Structures extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function canAccess(): bool 
    { 
        return true;
        /*$user=auth()->user();
        if (($user->can('view-any Statistic')) or (($user->can('view-any Gift'))) or (($user->can('view-any Meeting'))) or (($user->can('view-any Task'))) or (($user->can('view-any Employee')))){
            return true;
        } else {
            return false;
        }*/
    }
}

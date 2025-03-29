<?php
 
namespace Bishopm\Methodist\Filament\Pages;

use Filament\Pages\Dashboard as PagesDashboard;

class Dashboard extends PagesDashboard
{
    protected static string $view = 'methodist::dashboard';

    protected static ?int $navigationSort = -11;

    protected static ?string $navigationLabel = 'Methodist';

    protected static ?string $title = 'Dashboard';
}
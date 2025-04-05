<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource;
use Filament\Resources\Pages\Page;

class PreachingPlan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static string $resource = CircuitResource::class;
    protected static string $view = 'methodist::preaching-plan';
    protected static string $layout = 'methodist::layouts.no-sidebar';
    protected static ?string $navigationLabel = 'Preaching Plan';
    protected static ?string $title = 'Preaching Plan';
    protected static ?int $navigationSort = 3;

    public $record;
    public $today;

    public function getTitle(): string
    {
        return '';
    }
}

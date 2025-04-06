<?php

namespace Bishopm\Methodist\Filament\Pages;
 
use Closure;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Bishopm\Methodist\Filament\Clusters\Settings as SettingsCluster;
use Filament\Forms\Components\TagsInput;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class Settings extends BaseSettings
{
    protected static ?string $cluster = SettingsCluster::class;

    public static array|string $routeMiddleware = ['adminonly'];

    public function schema(): array|Closure
    {
        return [
            Tabs::make('Settings')
                ->schema([
                    Tabs\Tab::make('General')
                        ->columns(2)
                        ->schema([
                            TextInput::make('general.site_name')->required(),
                            TagsInput::make('general.servicetypes')->label('Default service types'),
                            TagsInput::make('general.leadership_roles'),
                            TagsInput::make('general.preacher_leadership_roles'),
                            TagsInput::make('general.minister_leadership_roles'),
                            TextInput::make('general.presiding_bishop')->label('Presiding Bishop'),
                            TextInput::make('general.general_secretary')->label('General Secretary')
                        ])
                ]),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Site settings';
    }
}
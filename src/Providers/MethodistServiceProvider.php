<?php namespace Bishopm\Methodist\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Bishopm\Methodist\Methodist;
use Bishopm\Methodist\Livewire\BarcodeScanner;
use Bishopm\Methodist\Livewire\BookReview;
use Bishopm\Methodist\Livewire\LoginForm;
use Bishopm\Methodist\Models\Individual;
use Bishopm\Methodist\Models\Pastor;
use Bishopm\Methodist\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MethodistServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'methodist');
        Paginator::useBootstrapFive();
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
        Blade::componentNamespace('Bishopm\\Methodist\\Resources\\Views\\Components', 'methodist');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/methodist.php', 'methodist');
        $this->app->singleton('methodist', function ($app) {
            return new Methodist;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['methodist'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../../config/methodist.php' => config_path('methodist.php'),
        ], 'methodist.config');

        // Publishing the views.
        // $this->publishes([
        //    __DIR__.'/../Resources' => public_path('vendor/bishopm'),
        // ], 'methodist.views');

        // Publishes assets.
        $this->publishes([
            __DIR__.'/../Resources/assets' => public_path('methodist'),
          ], 'assets');
        

        // Registering package commands.
        $this->commands([
            'Bishopm\Methodist\Console\Commands\InstallMethodist'
        ]);
    }
}

<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
		Schema::defaultStringLength(191);
    }
    // In AppServiceProvider or a helper file
function tenantLabel(string $key, string $default = ''): string
{
    return app('tenant')->getConfig("labels.{$key}", $default);
}
}

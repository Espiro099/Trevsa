<?php

namespace App\Providers;

use App\Http\Middleware\EnsurePermission;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('permission', EnsurePermission::class);
        
        // Auto-detect URL when accessed through network IP or reverse proxies
        // This ensures assets are loaded with the correct URL
        if (app()->environment('local')) {
            $host = request()->getHttpHost();
            // Update URL if accessing via network IP or external domain
            if ($host !== 'localhost' && $host !== '127.0.0.1' && !str_starts_with($host, '127.0.0.1')) {
                $url = request()->getScheme() . '://' . $host;
                config(['app.url' => $url]);
                \Illuminate\Support\Facades\URL::forceRootUrl($url);
            }
        }
    }
}

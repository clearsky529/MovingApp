<?php

namespace App\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AliasServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $loader = AliasLoader::getInstance();

        // Add your aliases
        $loader->alias('App', \Illuminate\Support\Facades\App::class);
        $loader->alias('Arr', \Illuminate\Support\Arr::class);
        $loader->alias('Artisan', \Illuminate\Support\Facades\Artisan::class);
        $loader->alias('Auth', \Illuminate\Support\Facades\Auth::class);
        $loader->alias('Blade', \Illuminate\Support\Facades\Blade::class);
        $loader->alias('Broadcast', \Illuminate\Support\Facades\Broadcast::class);
        $loader->alias('Bus', \Illuminate\Support\Facades\Bus::class);
        $loader->alias('Cache', \Illuminate\Support\Facades\Cache::class);
        $loader->alias('Config', \Illuminate\Support\Facades\Config::class);
        $loader->alias('Cookie', \Illuminate\Support\Facades\Cookie::class);
        $loader->alias('Crypt', \Illuminate\Support\Facades\Crypt::class);
        $loader->alias('DB', \Illuminate\Support\Facades\DB::class);
        $loader->alias('Eloquent', \Illuminate\Database\Eloquent\Model::class);
        $loader->alias('Event', \Illuminate\Support\Facades\Event::class);
        $loader->alias('File', \Illuminate\Support\Facades\File::class);
        $loader->alias('Gate', \Illuminate\Support\Facades\Gate::class);
        $loader->alias('Hash', \Illuminate\Support\Facades\Hash::class);
        $loader->alias('Http', \Illuminate\Support\Facades\Http::class);
        $loader->alias('Lang', \Illuminate\Support\Facades\Lang::class);
        $loader->alias('Log', \Illuminate\Support\Facades\Log::class);
        $loader->alias('Mail', \Illuminate\Support\Facades\Mail::class);
        $loader->alias('Notification', \Illuminate\Support\Facades\Notification::class);
        $loader->alias('Password', \Illuminate\Support\Facades\Password::class);
        $loader->alias('Queue', \Illuminate\Support\Facades\Queue::class);
        $loader->alias('Redirect', \Illuminate\Support\Facades\Redirect::class);
        $loader->alias('Redis', \Illuminate\Support\Facades\Redis::class);
        $loader->alias('Request', \Illuminate\Support\Facades\Request::class);
        $loader->alias('Response', \Illuminate\Support\Facades\Response::class);
        $loader->alias('Route', \Illuminate\Support\Facades\Route::class);
        $loader->alias('Schema', \Illuminate\Support\Facades\Schema::class);
        $loader->alias('Session', \Illuminate\Support\Facades\Session::class);
        $loader->alias('Storage', \Illuminate\Support\Facades\Storage::class);
        $loader->alias('Str', \Illuminate\Support\Str::class);
        $loader->alias('URL', \Illuminate\Support\Facades\URL::class);
        $loader->alias('Validator', \Illuminate\Support\Facades\Validator::class);
        $loader->alias('View', \Illuminate\Support\Facades\View::class);
        $loader->alias('Stripe', \Cartalyst\Stripe\Laravel\Facades\Stripe::class);
        $loader->alias('Keygen', \Keygen\Keygen::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

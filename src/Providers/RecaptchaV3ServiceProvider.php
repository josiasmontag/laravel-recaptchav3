<?php
/**
 * Created by Josias Montag
 * Date: 10/30/18 11:04 AM
 * Mail: josias@montag.info
 */

namespace Lunaweb\RecaptchaV3\Providers;

use Illuminate\Support\ServiceProvider;
use Lunaweb\RecaptchaV3\RecaptchaV3;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3 as RecaptchaV3Facade;

class RecaptchaV3ServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/recaptchav3.php' => config_path('recaptchav3.php'),
            ], 'config');
        }
        $this->mergeConfigFrom(__DIR__.'/../config/recaptchav3.php', 'recaptchav3');

        $this->app['validator']->extend('recaptchav3', function ($attribute, $value, $paramaters) {
            $action = $paramaters[0];
            $minScore = isset($paramaters[1]) ? (float)$paramaters[1] : 0.5;
            $score = RecaptchaV3Facade::verify($value, $action);
            return $score && $score >= $minScore;
        });

    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(RecaptchaV3::class);
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [RecaptchaV3::class];
    }

}

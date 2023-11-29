<?php

namespace Levi\LaravelRotateCaptcha;

use Illuminate\Support\ServiceProvider;
use Levi\LaravelRotateCaptcha\Support\File;

class CaptchaProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // lang
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'rotate.captcha');

        // router
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $storePath = config('rotate.captcha.storePath', 'rotate.captcha');
        $this->publishes([
            __DIR__.'/../config/rotate.captcha.php' => config_path('rotate.captcha.php'),
            __DIR__.'/../storage' => storage_path(sprintf('app/%s', $storePath)),
            __DIR__.'/../lang' => $this->app->langPath('vendor/rotate.captcha'),
        ]);
    }

    /**
     * Register bindings in the conttainer
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rotate.captcha.php', 'rotate.captcha');
        $this->app->singleton('rotate.captcha', function() {
            return new Captcha();
        });

        $this->app->singleton('rotate.captcha.file', function($_, $params) {
            $path = sprintf('%s%s%s',
                config('rotate.captcha.storePath'), DIRECTORY_SEPARATOR, $params['path'] ?? 'transform'
            );
            return File::make($path);
        });
    }
}

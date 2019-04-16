<?php

namespace Yiche\Ocr;

use Illuminate\Support\ServiceProvider;
use Yiche\Ocr\Services\OcrService;

class OcrServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // 加载路由
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->commands([OcrInstall::class]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('ycOcr', function () {
            return new OcrService();
        });
    }
}

<?php namespace Nht\Providers;

use Illuminate\Support\ServiceProvider;

class NhtFractalServiceProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * Register
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NhtFractal::class, function() {
            return new Nht\Hocs\Helpers\NhtFractal(new \League\Fractal\Manager);
        });
    }
}

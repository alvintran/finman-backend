<?php

namespace Nht\Providers;

use Illuminate\Support\ServiceProvider;

class BillProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\Nht\Hocs\Bills\BillRepository::class, \Nht\Hocs\Bills\DbBillRepository::class);
    }
}

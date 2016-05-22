<?php

namespace Nht\Providers;

use Illuminate\Support\ServiceProvider;

class BoardProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\Nht\Hocs\Boards\BoardRepository::class, \Nht\Hocs\Boards\DbBoardRepository::class);
    }
}

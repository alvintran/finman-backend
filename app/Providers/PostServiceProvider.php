<?php namespace Nht\Providers;

use Nht\Hocs\Posts\PostRepository;
use Nht\Hocs\Posts\DbPostRepository;
use Illuminate\Support\ServiceProvider;

class PostServiceProvider extends ServiceProvider
{
    /**
     * Register
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PostRepository::class, DbPostRepository::class);
    }
}

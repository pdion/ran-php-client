<?php namespace Ran\Client\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use Ran\Client\Services\RanService;

class RanServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Ran\Client\Contracts\RanServiceContract', function () {
            return new RanService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Ran\Client\Contracts\RanServiceContract'];
    }


}

?>

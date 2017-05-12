<?php
namespace MrVokia\MailHub;

use Illuminate\Support\ServiceProvider;
use ReflectionClass;

class MailHubServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('mailhub.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMailHub();

        // if( ! strstr(app()->version(), 'Lumen') ) {
        //     include __DIR__ . '/../routes.php';
        // }
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerMailHub()
    {
        // Api mail module
        $module = [
            'send' => new ReflectionClass('MrVokia\MailHub\MailHubSend'),
        ];

        $this->app->bind('mailhub', function ($app) use ($module) {
            return new MailHub($module);
        });

        $this->app->alias('mailhub', 'MrVokia\MailHub\MailHub');
    }

}

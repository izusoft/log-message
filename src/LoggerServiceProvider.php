<?php
namespace IzuSoft\Logger;

use Illuminate\Support\ServiceProvider;

class LoggerServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('LogMessage', LogMessageToChannels::class);
    }
}

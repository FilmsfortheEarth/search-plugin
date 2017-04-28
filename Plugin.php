<?php namespace Ffte\Search;

use Ffte\Search\Classes\EventSubscriber;
use System\Classes\PluginBase;
use Event;

class Plugin extends PluginBase
{
    public function boot()
    {
        Event::subscribe(EventSubscriber::class);
    }

    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }
}

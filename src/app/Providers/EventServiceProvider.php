<?php

namespace Mastery\Master\App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //  $events->listen('eloquent.created: App\Models\Node', '\App\Listeners\CreateNode');
        //
        Event::listen('eloquent.created: Mastery\Master\App\Models\Node', [Mastery\Master\App\Listeners\CreateNode::class, 'handle']);

    }
}

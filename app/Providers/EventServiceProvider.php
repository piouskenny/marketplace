<?php

namespace App\Providers;

use App\Events\ConnectionActivated;
use App\Events\ConnectionRequestAccepted;
use App\Events\ConnectionRequestCreated;
use App\Events\ConnectionRequestDeclined;
use App\Listeners\SendConnectionAcceptedNotification;
use App\Listeners\SendConnectionActivatedNotification;
use App\Listeners\SendConnectionDeclinedNotification;
use App\Listeners\SendConnectionRequestNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ConnectionRequestCreated::class => [
            SendConnectionRequestNotification::class,
        ],

        ConnectionRequestAccepted::class => [
            SendConnectionAcceptedNotification::class,
        ],

        ConnectionRequestDeclined::class => [
            SendConnectionDeclinedNotification::class,
        ],

        ConnectionActivated::class => [
            SendConnectionActivatedNotification::class,
        ],
    ];

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

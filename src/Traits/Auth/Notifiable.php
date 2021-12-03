<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Traits\Auth;

use Illuminate\Notifications\RoutesNotifications;

trait Notifiable
{
    use RoutesNotifications;
}
<?php

namespace App\GraphQL\Queries;

use App\Models\Notification;

final class NotificationQuery
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function list($_, array $args)
    {
        return Notification::list($args);
    }
}
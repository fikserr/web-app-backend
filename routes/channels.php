<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('basket.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
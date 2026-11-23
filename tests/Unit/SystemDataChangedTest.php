<?php

use App\Events\SystemDataChanged;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

test('system updates broadcast a private channel after database commit', function () {
    $event = new SystemDataChanged();
    $channels = $event->broadcastOn();

    expect($event)->toBeInstanceOf(ShouldBroadcastNow::class);
    expect($event)->toBeInstanceOf(ShouldDispatchAfterCommit::class);
    expect($channels)->toHaveCount(1);
    expect($channels[0])->toBeInstanceOf(PrivateChannel::class);
    expect($channels[0]->name)->toBe('private-system.updates');
});

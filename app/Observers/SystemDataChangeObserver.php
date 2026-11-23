<?php

namespace App\Observers;

use App\Events\SystemDataChanged;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Model;

class SystemDataChangeObserver implements ShouldHandleEventsAfterCommit
{
    public function saved(Model $model): void
    {
        broadcast(new SystemDataChanged());
    }

    public function deleted(Model $model): void
    {
        broadcast(new SystemDataChanged());
    }
}

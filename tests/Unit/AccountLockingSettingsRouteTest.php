<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AccountLockingSettingsRouteTest extends TestCase
{
    public function test_account_locking_settings_form_routes_use_the_staff_it_route_names(): void
    {
        foreach ([
            'staff.it.account-locking.store',
            'staff.it.account-locking.unlock',
            'staff.it.account-locking.destroy',
        ] as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route {$routeName} should be registered.");
        }
    }
}

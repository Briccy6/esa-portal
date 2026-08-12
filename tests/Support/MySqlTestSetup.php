<?php

namespace Tests\Support;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

trait MySqlTestSetup
{
    protected function setUpMySqlConnection(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('PHP PDO MySQL driver is not available.');
        }

        Config::set('database.default', 'mysql');
        Config::set('database.connections.mysql.host', '127.0.0.1');
        Config::set('database.connections.mysql.port', '3306');
        Config::set('database.connections.mysql.database', 'school');
        Config::set('database.connections.mysql.username', 'root');
        Config::set('database.connections.mysql.password', '');
        Config::set('database.connections.mysql.charset', 'utf8mb4');
        Config::set('database.connections.mysql.collation', 'utf8mb4_unicode_ci');
        Config::set('database.connections.mysql.prefix', '');
        Config::set('database.connections.mysql.strict', true);

        $this->app['config']->set('database.default', 'mysql');
        $this->app['config']->set('database.connections.mysql.host', '127.0.0.1');
        $this->app['config']->set('database.connections.mysql.port', '3306');
        $this->app['config']->set('database.connections.mysql.database', 'school');
        $this->app['config']->set('database.connections.mysql.username', 'root');
        $this->app['config']->set('database.connections.mysql.password', '');
        $this->app['config']->set('database.connections.mysql.charset', 'utf8mb4');
        $this->app['config']->set('database.connections.mysql.collation', 'utf8mb4_unicode_ci');
        $this->app['config']->set('database.connections.mysql.prefix', '');
        $this->app['config']->set('database.connections.mysql.strict', true);
        $this->app['config']->set('database.default', 'mysql');

        $this->app['db']->setDefaultConnection('mysql');
        DB::purge('mysql');
        DB::reconnect('mysql');

        try {
            DB::connection('mysql')->getPdo();
        } catch (\Exception $e) {
            $this->markTestSkipped('MySQL connection is not available: ' . $e->getMessage());
        }
    }
}

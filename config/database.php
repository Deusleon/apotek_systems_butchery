<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'softlink'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        /*ours both online and offline*/
        'softlink' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_CONNECTION', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*ours both online and offline*/


        /*online only*/

        /*godwin malapa*/
        'malapa' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'godwin_malapa',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*godwin malapa end*/

        /*demo*/
        'demo' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'demo_inventory',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*demo end*/

        /*fahari*/
        'fahari' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'fahari',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*fahari end*/

        /*sandr*/
        'sandr' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'sandr',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*sandr end*/

        /*malaika*/
        'malaika' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'malaika',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*malaika end*/

        /*silver*/
        'silver' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'silver',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*silver end*/

        /*great_tyres*/
        'great_tyres' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'great_tyres',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*great_tyres end*/

        /*roia*/
        'roia' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'roia',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*roia end*/
        
        /*eternity*/
        'eternity' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'eternity',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*eternity end*/

        /*mbokomu*/
        'mbokomu' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'mbokomu',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*mbokomu end*/

        /*maina*/
        'maina' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'maina',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*maina end*/
        /*godwin_buguruni */
        'godwin_buguruni' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'godwin_buguruni',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*godwin_buguruni end*/

        /*Artfor*/
        'artfor' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'artfor',
            'username' => 'root',
            'password' => 'Apotek@dba1',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        /*Artfor end*/

        /*online only end*/

        /*offline only*/
        /*apotek_inventory*/
//        'Apotek_Inventory' => [
//            'driver' => 'mysql',
//            'url' => env('DATABASE_URL'),
//            'host' => '127.0.0.1',
//            'port' => '3306',
//            'database' => 'apotek_inventory',
//            'username' => 'root',
//            'password' => '',
//            'unix_socket' => env('DB_SOCKET', ''),
//            'charset' => 'utf8mb4',
//            'collation' => 'utf8mb4_unicode_ci',
//            'prefix' => '',
//            'prefix_indexes' => true,
//            'strict' => false,
//            'engine' => null,
//            'options' => extension_loaded('pdo_mysql') ? array_filter([
//                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
//            ]) : [],
//        ],
//
//        /*apotek_updated*/
//        'Apotek_Updated' => [
//            'driver' => 'mysql',
//            'url' => env('DATABASE_URL'),
//            'host' => '127.0.0.1',
//            'port' => '3306',
//            'database' => 'apotek_updated',
//            'username' => 'root',
//            'password' => '',
//            'unix_socket' => env('DB_SOCKET', ''),
//            'charset' => 'utf8mb4',
//            'collation' => 'utf8mb4_unicode_ci',
//            'prefix' => '',
//            'prefix_indexes' => true,
//            'strict' => false,
//            'engine' => null,
//            'options' => extension_loaded('pdo_mysql') ? array_filter([
//                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
//            ]) : [],
//        ],
//
//        /*great_tyre*/
//        'GREAT_TYRES' => [
//            'driver' => 'mysql',
//            'url' => env('DATABASE_URL'),
//            'host' => '127.0.0.1',
//            'port' => '3306',
//            'database' => 'great_tyre',
//            'username' => 'root',
//            'password' => '',
//            'unix_socket' => env('DB_SOCKET', ''),
//            'charset' => 'utf8mb4',
//            'collation' => 'utf8mb4_unicode_ci',
//            'prefix' => '',
//            'prefix_indexes' => true,
//            'strict' => false,
//            'engine' => null,
//            'options' => extension_loaded('pdo_mysql') ? array_filter([
//                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
//            ]) : [],
//        ],
//
//        /*clean*/
//        'clean' => [
//            'driver' => 'mysql',
//            'url' => env('DATABASE_URL'),
//            'host' => '127.0.0.1',
//            'port' => '3306',
//            'database' => 'clean_inventory',
//            'username' => 'root',
//            'password' => '',
//            'unix_socket' => env('DB_SOCKET', ''),
//            'charset' => 'utf8mb4',
//            'collation' => 'utf8mb4_unicode_ci',
//            'prefix' => '',
//            'prefix_indexes' => true,
//            'strict' => false,
//            'engine' => null,
//            'options' => extension_loaded('pdo_mysql') ? array_filter([
//                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
//            ]) : [],
//        ],

        /*offline only end*/

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'predis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'predis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],

    ],

];

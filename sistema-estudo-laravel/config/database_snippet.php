<?php
/*
 * NÃO é um arquivo para rodar sozinho.
 * Copie o bloco abaixo para dentro do array 'connections' do seu
 * config/database.php (que já vem no Laravel instalado via composer).
 */

'connections' => [

    // ... mysql, sqlite, pgsql já existem por padrão ...

    'mongodb' => [
        'driver' => 'mongodb',
        'host' => env('MONGO_DB_HOST', '127.0.0.1'),
        'port' => env('MONGO_DB_PORT', 27017),
        'database' => env('MONGO_DB_DATABASE', 'estudo_laravel_logs'),
        'username' => env('MONGO_DB_USERNAME', ''),
        'password' => env('MONGO_DB_PASSWORD', ''),
        'options' => [
            'database' => env('MONGO_DB_AUTH_DATABASE', 'admin'),
        ],
    ],

],

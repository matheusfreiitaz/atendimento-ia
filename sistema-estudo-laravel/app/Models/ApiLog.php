<?php

namespace App\Models;

// Model MongoDB - usa o pacote mongodb/laravel-mongodb.
// A grande diferença para um Model comum: a "connection" aponta
// para a conexão mongodb definida em config/database.php, e não
// existe migration (Mongo é schemaless).
use MongoDB\Laravel\Eloquent\Model;

class ApiLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'api_logs';

    protected $fillable = [
        'metodo',
        'rota',
        'url_completa',
        'parametros',
        'status_code',
        'tempo_resposta_ms',
        'ip',
        'user_agent',
    ];
}

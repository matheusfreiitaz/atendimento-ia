<?php
/*
 * NÃO é um arquivo para rodar sozinho.
 * No Laravel 11, o registro de middlewares fica em bootstrap/app.php.
 * Adicione o LogApiRequests dentro do grupo "api", assim:
 */ 

use App\Http\Middleware\LogApiRequests;

return Illuminate\Foundation\Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',   
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Illuminate\Foundation\Configuration\Middleware $middleware) {
        $middleware->api(append: [
            LogApiRequests::class,
        ]);
    })
    ->withExceptions(function (Illuminate\Foundation\Configuration\Exceptions $exceptions) {
        //
    })->create();

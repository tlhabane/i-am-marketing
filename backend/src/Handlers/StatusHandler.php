<?php

namespace App\Handlers;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class StatusHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'online',
            'timestamp' => time(),
            // The CSRF token is injected into headers by the middleware,
            // but we can also include it here for explicit frontend consumption.
            'csrf_token' => $_SESSION['csrf_token'] ?? null,
            'version' => '1.0.0-canvas'
        ]));
    }
}
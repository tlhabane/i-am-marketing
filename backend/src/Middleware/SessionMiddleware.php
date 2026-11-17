<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Start the session if it hasn't been started yet
        if (session_status() === PHP_SESSION_NONE) {
            // Secure settings: Only allow cookies via HTTP, prevent JS access
            session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => isset($_SERVER['HTTPS']),
                'use_strict_mode' => true,
            ]);
        }

        // Optional: Regenerate ID periodically to prevent session fixation
        if (!isset($_SESSION['CREATED'])) {
            $_SESSION['CREATED'] = time();
        } elseif (time() - $_SESSION['CREATED'] > 1800) { // 30 mins
            session_regenerate_id(true);
            $_SESSION['CREATED'] = time();
        }

        return $handler->handle($request);
    }
}
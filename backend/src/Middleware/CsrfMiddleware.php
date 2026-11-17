<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Response;
use Random\RandomException;

class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * @throws RandomException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 1. Ensure a token exists in the session
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // 2. Only validate for "unsafe" methods
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $parsedBody = $request->getParsedBody();

            // Look for token in Header (cleanest for AJAX) or Parsed Body
            $token = $request->getHeaderLine('X-CSRF-TOKEN')
                ?: ($parsedBody['csrf_token'] ?? '');

            if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
                return new Response(403, ['Content-Type' => 'application/json'], json_encode([
                    'status' => 'error',
                    'message' => 'CSRF token mismatch or missing.'
                ]));
            }
        }

        // 3. Add token to response headers so the frontend can update its store
        $response = $handler->handle($request);
        return $response->withHeader('X-CSRF-TOKEN', $_SESSION['csrf_token']);
    }
}
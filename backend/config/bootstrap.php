<?php

use App\Handlers\RequestDispatcher;
use App\Middleware\CsrfMiddleware;
use App\Middleware\JsonBodyParserMiddleware;
use App\Middleware\SanitizeFormDataMiddleware;
use App\Middleware\SessionMiddleware;
use DI\ContainerBuilder;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use function FastRoute\simpleDispatcher;

// Handlers & Middleware

try {
    $container = (new ContainerBuilder())->addDefinitions(__DIR__ . '/../config/container.php')->build();

    $routeDispatcher = simpleDispatcher(require __DIR__ . '/../config/routes.php');

    $psr17Factory = new Psr17Factory();
    $request = (new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory))->fromGlobals();

    $detector = $container->get(\App\Util\BasePathDetector::class);
    $basePath = $detector->getBasePath();

    $uriPath = $request->getUri()->getPath();
    // Strip the base path from the URI to get the internal route
    // Example: "/the-canvas-co/backend/captcha" -> "/captcha"
    if (!empty($basePath) && str_contains($uriPath, $basePath)) {
        $requestPath = substr($uriPath, strlen($basePath));
    } else {
        $requestPath = $uriPath;
    }

    // Ensure it starts with / and is clean for the dispatcher
    $requestPath = '/' . ltrim($requestPath, '/');
    // DEBUG TIP: Add this temporarily to see what FastRoute is actually seeing
    // file_put_contents('debug_path.txt', "Method: " . $request->getMethod() . " | Path: " . $requestPath);
    $routeInfo = $routeDispatcher->dispatch($request->getMethod(), $requestPath);

    switch ($routeInfo[0]) {
        case \FastRoute\Dispatcher::NOT_FOUND:
            $response = new \Nyholm\Psr7\Response(404, [], json_encode(['error' => 'Not Found']));
            break;

        case \FastRoute\Dispatcher::FOUND:
            $handlerClass = $routeInfo[1];
            $pipeline = new RequestDispatcher($container->get($handlerClass));

            // --- GLOBAL MIDDLEWARE ---
            // 1. Session Management, JSON body parser and CSRF protection (Top of stack)
            $pipeline->add($container->get(SessionMiddleware::class));
            $pipeline->add($container->get(JsonBodyParserMiddleware::class));
            $pipeline->add($container->get(CsrfMiddleware::class));

            // 2. Data Sanitization (for all POST)
            if ($request->getMethod() === 'POST') {
                $pipeline->add($container->get(SanitizeFormDataMiddleware::class));
            }

            // --- CONDITIONAL MIDDLEWARE ---
            /*if ($handlerClass === BookACallHandler::class ||
                $handlerClass === ContactMailHandler::class) {
                $pipeline->add($container->get(CaptchaValidationMiddleware::class));
                $pipeline->add($container->get(FileUploadMiddleware::class));
            }*/

            $response = $pipeline->handle($request);
            break;

        default:
            $response = new \Nyholm\Psr7\Response(405, [], json_encode(['error' => 'Method Not Allowed']));
            break;
    }

    // Output
    http_response_code($response->getStatusCode());
    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) header(sprintf('%s: %s', $name, $value), false);
    }
    echo (string)$response->getBody();

} catch (\Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
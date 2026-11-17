<?php

use FastRoute\RouteCollector;

return function (RouteCollector $route) {
    $route->addRoute('GET',  '/', \App\Handlers\StatusHandler::class); // Health check & CSRF Init
    $route->addRoute('POST', '/newsletter', \App\Handlers\NewsletterHandler::class);
};

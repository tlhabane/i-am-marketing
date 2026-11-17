<?php

$allowedOrigins = [
    'https://kreativware.co.za',
    'https://ais-dev-wr7ik67ah4trtspl7v2ger-22262802877.europe-west1.run.app',
    'http://localhost:3000'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Vary: Origin");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, PATCH, PUT, OPTIONS, GET, DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-CSRF-TOKEN");
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

(require_once(__DIR__ . '/../vendor/autoload.php'));
(require_once(__DIR__ . '/../config/bootstrap.php'));
(require_once(__DIR__ . '/../config/routes.php'));

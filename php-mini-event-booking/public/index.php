<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\PageController;
use App\Controllers\ShowController;
use App\Controllers\BookingController;
use App\Support\Env;
use App\Support\Response;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$dotenv->required(['APP_NAME', 'APP_ENV', 'APP_DEBUG', 'APP_URL', 'COMPANY_NAME', 'MAX_TICKETS_PER_BOOKING']);
$dotenv->required('APP_DEBUG')->isBoolean();
$dotenv->required('MAX_TICKETS_PER_BOOKING')->isInteger();

error_reporting(E_ALL);
if (Env::get('APP_ENV', 'prod') === 'dev' && Env::bool('APP_DEBUG', false)) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    ini_set('log_errors', '1');
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', dirname(__DIR__) . '/storage/logs/php-error.log');
}

$config = require dirname(__DIR__) . '/config/app.php';
$shows = require dirname(__DIR__) . '/src/Data/shows.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/' && $method === 'GET') {
    $controller = new PageController();
    $data = $controller->home($config, $shows);
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

if ($path === '/shows' && $method === 'GET') {
    (new ShowController())->list($shows);
}

if ($path === '/shows' && $method === 'HEAD') {
    (new ShowController())->head();
}

if ($path === '/shows' && !in_array($method, ['GET', 'HEAD'], true)) {
    Response::json(405, [
        'error' => 'Method Not Allowed'
    ], [
        'Allow' => 'GET, HEAD'
    ]);
}

if ($path === '/bookings' && $method === 'POST') {
    (new BookingController())->book($shows, $config);
}

if ($path === '/bookings' && $method === 'OPTIONS') {
    (new BookingController())->options();
}

if ($path === '/bookings' && !in_array($method, ['POST', 'OPTIONS'], true)) {
    Response::json(405, [
        'error' => 'Method Not Allowed'
    ], [
        'Allow' => 'POST, OPTIONS'
    ]);
}

Response::json(404, [
    'error' => 'Not Found'
]);
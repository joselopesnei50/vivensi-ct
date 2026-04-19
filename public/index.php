<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

// Load .env
if (file_exists(ROOT . '/.env')) {
    foreach (file(ROOT . '/.env') as $line) {
        $line = trim($line);
        if (!$line || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v);
        putenv(trim($k) . '=' . trim($v));
    }
}

function env(string $key, mixed $default = null): mixed {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// Autoloader
spl_autoload_register(function (string $class): void {
    $file = ROOT . '/' . str_replace(['\\', 'App/'], ['/', 'app/'], $class) . '.php';
    if (file_exists($file)) require_once $file;
});

// Helpers globais
function url(string $path = ''): string {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    return url('/assets/' . ltrim($path, '/'));
}

function e(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

function csrf_field(): string {
    return '<input type="hidden" name="_token" value="' . \App\Core\Request::csrf() . '">';
}

function auth(): ?\App\Core\Auth {
    return null; // static class, use Auth:: directly
}

function flash(string $type): ?string {
    return \App\Core\Flash::get($type);
}

// Load configs
date_default_timezone_set('America/Sao_Paulo');
mb_internal_encoding('UTF-8');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Error handling
$appConfig = require ROOT . '/config/app.php';
if (!empty($appConfig['debug'])) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Initialize Router
$router = new \App\Core\Router();
require ROOT . '/routes/web.php';

// Dispatch
$method = $_SERVER['REQUEST_METHOD'];
$uri    = $_SERVER['REQUEST_URI'];
$router->dispatch($method, $uri);

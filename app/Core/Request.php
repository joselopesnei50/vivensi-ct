<?php
namespace App\Core;

class Request
{
    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    public static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public static function sanitize(string $key, mixed $default = null): string
    {
        $val = self::input($key, $default);
        return htmlspecialchars(strip_tags(trim((string)$val)), ENT_QUOTES, 'UTF-8');
    }

    public static function csrf(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $expected = $_SESSION['csrf_token'] ?? '';
        if (!$expected) return false;

        // 1. Standard form POST field
        $token = self::post('_token') ?? self::get('_token');

        // 2. Custom header (used by JSON AJAX requests — safe because custom headers
        //    require CORS preflight, preventing cross-origin forgery)
        if (!$token) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        }

        // 3. JSON request body (Content-Type: application/json)
        if (!$token && str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
            $body  = json_decode(file_get_contents('php://input'), true);
            $token = $body['_token'] ?? null;
        }

        return hash_equals($expected, (string)($token ?? ''));
    }

    public static function ip(): string
    {
        // Always use REMOTE_ADDR — never trust X-Forwarded-For from untrusted sources
        // (spoofable by attackers to bypass rate limiting)
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    public static function back(): void
    {
        // Validate referer is on the same host to prevent open redirect
        $ref  = $_SERVER['HTTP_REFERER'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($ref && $host && parse_url($ref, PHP_URL_HOST) === $host) {
            header("Location: {$ref}");
        } else {
            header('Location: ' . url('/dashboard'));
        }
        exit;
    }
}

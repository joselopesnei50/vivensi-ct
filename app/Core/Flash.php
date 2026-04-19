<?php
namespace App\Core;

class Flash
{
    public static function set(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'][$type] = $message;
    }

    public static function success(string $message): void { self::set('success', $message); }
    public static function error(string $message): void   { self::set('error', $message); }
    public static function info(string $message): void    { self::set('info', $message); }
    public static function warning(string $message): void { self::set('warning', $message); }

    public static function get(string $type): ?string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $msg = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $msg;
    }

    public static function has(string $type): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION['flash'][$type]);
    }

    public static function all(): array
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }
}

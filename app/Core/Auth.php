<?php
namespace App\Core;

class Auth
{
    private const MAX_ATTEMPTS  = 5;
    private const LOCKOUT_SECS  = 300; // 5 minutos

    public static function login(array $user): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Regenerate BEFORE writing new session data (session fixation prevention)
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['nome'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['tenant_id']  = $user['tenant_id'];
        // Clear brute-force counters on successful login
        unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }

    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        return [
            'id'        => $_SESSION['user_id'],
            'nome'      => $_SESSION['user_name'],
            'email'     => $_SESSION['user_email'],
            'role'      => $_SESSION['user_role'],
            'tenant_id' => $_SESSION['tenant_id'],
        ];
    }

    public static function id(): ?int
    {
        return self::check() ? (int)$_SESSION['user_id'] : null;
    }

    public static function tenantId(): ?int
    {
        return self::check() ? (int)$_SESSION['tenant_id'] : null;
    }

    public static function role(): ?string
    {
        return self::check() ? $_SESSION['user_role'] : null;
    }

    public static function isSuperAdmin(): bool
    {
        return self::role() === 'super_admin';
    }

    public static function isAdmin(): bool
    {
        return in_array(self::role(), ['super_admin', 'admin']);
    }

    /** Returns true if the current IP is locked out due to too many failed attempts. */
    public static function isLockedOut(): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $until = $_SESSION['login_locked_until'] ?? 0;
        if ($until && time() < $until) return true;
        // Lock expired — reset
        if ($until && time() >= $until) {
            unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
        }
        return false;
    }

    /** Records a failed login attempt and locks out if threshold is reached. */
    public static function recordFailedAttempt(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= self::MAX_ATTEMPTS) {
            $_SESSION['login_locked_until'] = time() + self::LOCKOUT_SECS;
        }
    }

    public static function attempt(string $email, string $password): bool
    {
        if (self::isLockedOut()) return false;

        $user = Database::selectOne("SELECT * FROM users WHERE email = ? AND ativo = 1 LIMIT 1", [$email]);
        if (!$user || !password_verify($password, $user['password'])) {
            self::recordFailedAttempt();
            // Log failed attempt (user_id unknown — log with null)
            AuditLog::auth('login.failed');
            return false;
        }

        self::login($user);
        Database::query("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
        AuditLog::auth('login.success', (int)$user['id']);
        return true;
    }

    public static function logoutCurrent(): void
    {
        AuditLog::auth('logout');
        self::logout();
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: ' . url('/login'));
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireAuth();
        if (!self::isAdmin()) {
            http_response_code(403);
            die('Acesso negado.');
        }
    }

    public static function requireSuperAdmin(): void
    {
        self::requireAuth();
        if (!self::isSuperAdmin()) {
            http_response_code(403);
            die('Acesso negado.');
        }
    }
}

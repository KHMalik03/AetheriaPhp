<?php

class Session {
    private const LIFETIME = 6 * 60 * 60; // 6 hours in seconds

    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params(self::LIFETIME);
            ini_set('session.gc_maxlifetime', self::LIFETIME);
            session_start();
        }
    }

    public static function set(int $id, string $username, string $role): void {
        $_SESSION['user_id']   = $id;
        $_SESSION['username']  = $username;
        $_SESSION['role']      = $role;
        $_SESSION['logged_at'] = time();
    }

    public static function isLoggedIn(): bool {
        if (!isset($_SESSION['user_id'], $_SESSION['logged_at'])) {
            return false;
        }
        if (time() - $_SESSION['logged_at'] > self::LIFETIME) {
            self::destroy();
            return false;
        }
        return true;
    }

    public static function isAdmin(): bool {
        return self::isLoggedIn() && $_SESSION['role'] === 'admin';
    }

    public static function get(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    public static function destroy(): void {
        session_unset();
        session_destroy();
    }
}

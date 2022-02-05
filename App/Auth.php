<?php

namespace App;

use \App\Models\User;
use \App\Models\RememberedLogins;

/**
 * Auth class
 * 
 * PHP version 7.4
 */
class Auth
{
    /**
     * Login
     * 
     * @param User $user
     * @param bool $remember_me Remember the login if true
     * 
     * @return void
     */
    public static function login(User $user, bool $remember_me): void
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;

        if ($remember_me) {
            if ($user->rememberLogin()) {
                setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');
            }
        }
    }

    /**
     * Logout
     * 
     * @return void
     */
    public static function logout(): void
    {
        // Destruir todas las variables de sesión.
        $_SESSION = array();

        // Si se desea destruir la sesión completamente, borre también la cookie de sesión.
        // Nota: ¡Esto destruirá la sesión, y no la información de la sesión!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Finalmente, destruir la sesión.
        session_destroy();

        static::forgetLogin();
    }

    /**
     * Remember the originally requested page in the session
     * 
     * @return void
     */
    public static function rememberRequestedPage(): void
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the originally requested page to return to after requiring login, or default to the homepage
     * 
     * @return string
     */
    public static function getReturnToPage(): string
    {
        return $_SESSION['return_to'] ?? '/';
    }

    /**
     * Get the current logged-in user, from the session or the remember-me cookie
     * 
     * @return mixed The user model or null if not logged in.
     */
    public static function getUser()
    {
        if (isset($_SESSION['user_id'])) {
            return User::findById($_SESSION['user_id']);
        } else {
            return static::loginFromRememberCookie();
        }
    }

    /**
     * Login the user from a remembered login cookie
     * 
     * @return mixed The user model if login cookie found; null otherwise
     */
    protected static function loginFromRememberCookie()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if (isset($cookie)) {

            $remembered_login = RememberedLogins::findByToken($cookie);

            if ($remembered_login && !$remembered_login->hasExpired()) {

                $user = $remembered_login->getUser();

                static::login($user, false);

                return $user;
            }
        }
    }

    /**
     * Forget the remembered login, if present
     * 
     * @return void
     */
    protected static function forgetLogin()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if (isset($cookie)) {

            $remembered_login = RememberedLogins::findByToken($cookie);

            if ($remembered_login) {
                $remembered_login->delete();
            }
        }

        setcookie('remember_me', '', time() - 3600); // set to expire in the past
    }
}

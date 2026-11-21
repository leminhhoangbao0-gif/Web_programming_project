<?php
declare(strict_types=1);

namespace Atlas\Core;

final class Csrf
{
    public static function token(): string
    {
        $token = Session::get('_csrf');
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf', $token);
        }
        return $token;
    }

    public static function field(): string
    {
        $escaped = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf" value="' . $escaped . '">';
    }

    public static function verify(Request $r): void
    {
        $sessionToken = self::token();
        $submitted = $r->input('_csrf');

        if (!is_string($submitted) || $submitted === '') {
            $submitted = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }

        if (!is_string($submitted) || !hash_equals($sessionToken, $submitted)) {
            Response::forbidden();
        }
    }
}

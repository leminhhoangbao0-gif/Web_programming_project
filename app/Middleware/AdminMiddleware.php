<?php

namespace Atlas\Middleware;

use Atlas\Core\Config;
use Atlas\Services\AuthService;

class AdminMiddleware implements Middleware
{
    private AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function handle(): void
    {
        if (!$this->auth->check('admin')) {
            header('Location: /admin/login');
            exit;
        }

        if ($this->auth->isTimedOut(
            Config::get('auth.admin_idle_seconds')
        )) {
            $this->auth->logout();

            header('Location: /admin/login?expired=1');
            exit;
        }

        $this->auth->touchActivity();
    }
}
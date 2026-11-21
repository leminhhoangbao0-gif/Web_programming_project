<?php
declare(strict_types=1);

namespace Atlas\Middleware;

use Atlas\Core\Request;
use Atlas\Core\Response;
use Atlas\Services\AuthService;

final class CustomerMiddleware implements Middleware
{
    public function handle(Request $r): void
    {
        $auth = new AuthService();

        if (!$auth->check('customer')) {
            if ($r->isJson()) {
                Response::error('UNAUTHENTICATED', 'Authentication required.', 401);
            }

            $redirect = urlencode($r->path());
            Response::redirect('/login?redirect=' . $redirect);
        }

        $auth->touchActivity();
    }
}

<?php
declare(strict_types=1);

namespace Atlas\Controllers\Shop;

use Atlas\Core\Csrf;
use Atlas\Core\Request;
use Atlas\Core\Response;
use Atlas\Core\Session;
use Atlas\Core\Validator;
use Atlas\Core\View;
use Atlas\Services\AuthService;
use DomainException;

final class AuthController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    public function showRegister(Request $req): void
    {
        if ($this->auth->check('customer')) {
            Response::redirect('/');
        }

        View::render('shop/auth/register', [
            'title'    => 'Đăng ký tài khoản',
            'extraCss' => ['/assets/css/shop/account.css'],
        ]);
    }

    public function register(Request $req): void
    {
        Csrf::verify($req);

        $v = (new Validator($req->all()))
            ->required('full_name', 'Họ và tên')->minLength('full_name', 2, 'Họ và tên')->maxLength('full_name', 100, 'Họ và tên')
            ->required('email', 'Email')->email('email', 'Email')->maxLength('email', 150, 'Email')
            ->phoneVn('phone', 'Số điện thoại')
            ->required('password', 'Mật khẩu')->minLength('password', 8, 'Mật khẩu')
            ->required('password_confirmation', 'Xác nhận mật khẩu')->same('password_confirmation', 'password', 'Xác nhận mật khẩu');

        if (!$v->passes()) {
            Session::flash('errors', $v->errors());
            $old = $req->all();
            unset($old['password'], $old['password_confirmation']);
            Session::flash('old', $old);
            Response::redirect('/register');
        }

        try {
            $this->auth->register(
                (string) $req->input('full_name'),
                (string) $req->input('email'),
                $req->input('phone') !== '' ? (string) $req->input('phone') : null,
                (string) $req->input('password')
            );
        } catch (DomainException $e) {
            if ($e->getMessage() === 'EMAIL_TAKEN') {
                Session::flash('errors', ['email' => 'Email này đã được sử dụng.']);
                $old = $req->all();
                unset($old['password'], $old['password_confirmation']);
                Session::flash('old', $old);
                Response::redirect('/register');
            }
            throw $e;
        }

        Session::flash('success', 'Đăng ký tài khoản thành công!');
        Response::redirect('/');
    }

    public function showLogin(Request $req): void
    {
        if ($this->auth->check('customer')) {
            Response::redirect('/');
        }

        $redirect = $this->sanitizeRedirect((string) $req->query('redirect', ''));

        View::render('shop/auth/login', [
            'title'    => 'Đăng nhập',
            'redirect' => $redirect,
            'extraCss' => ['/assets/css/shop/account.css'],
        ]);
    }

    public function login(Request $req): void
    {
        Csrf::verify($req);

        $redirect = $this->sanitizeRedirect((string) $req->input('redirect', ''));

        $v = (new Validator($req->all()))
            ->required('email', 'Email')->email('email', 'Email')
            ->required('password', 'Mật khẩu');

        if (!$v->passes()) {
            Session::flash('errors', $v->errors());
            Session::flash('old', ['email' => $req->input('email')]);
            Response::redirect('/login' . ($redirect !== '/' ? '?redirect=' . urlencode($redirect) : ''));
        }

        $user = $this->auth->attemptLogin(
            (string) $req->input('email'),
            (string) $req->input('password'),
            'customer'
        );

        if ($user === null) {
            Session::flash('error', 'Email hoặc mật khẩu không chính xác.');
            Session::flash('old', ['email' => $req->input('email')]);
            Response::redirect('/login' . ($redirect !== '/' ? '?redirect=' . urlencode($redirect) : ''));
        }

        Session::flash('success', 'Đăng nhập thành công!');
        Response::redirect($redirect);
    }

    public function logout(Request $req): void
    {
        Csrf::verify($req);
        $this->auth->logout();
        Response::redirect('/');
    }

    private function sanitizeRedirect(string $path): string
    {
        if ($path !== '' && str_starts_with($path, '/') && !str_starts_with($path, '//')) {
            return $path;
        }
        return '/';
    }
}

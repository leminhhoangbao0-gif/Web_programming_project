<?php
declare(strict_types=1);

namespace Atlas\Controllers\Shop;

use Atlas\Core\Csrf;
use Atlas\Core\Request;
use Atlas\Core\Response;
use Atlas\Core\Session;
use Atlas\Core\Validator;
use Atlas\Core\View;
use Atlas\Repositories\UserRepository;
use Atlas\Services\AuthService;

final class AccountController
{
    private AuthService $auth;
    private UserRepository $users;

    public function __construct()
    {
        $this->auth  = new AuthService();
        $this->users = new UserRepository();
    }

    public function profile(Request $req): void
    {
        $user = $this->auth->currentUser();
        if ($user === null) {
            Response::redirect('/login');
        }

        View::render('shop/account/profile', [
            'title'    => 'Hồ sơ cá nhân',
            'user'     => $user,
            'extraCss' => ['/assets/css/shop/account.css'],
        ]);
    }

    public function updateProfile(Request $req): void
    {
        Csrf::verify($req);
        $user = $this->auth->currentUser();
        if ($user === null) {
            Response::redirect('/login');
        }

        $v = (new Validator($req->all()))
            ->required('full_name', 'Họ và tên')->minLength('full_name', 2, 'Họ và tên')->maxLength('full_name', 100, 'Họ và tên')
            ->phoneVn('phone', 'Số điện thoại');

        if (!$v->passes()) {
            Session::flash('errors', $v->errors());
            Session::flash('old', $req->all());
            Response::redirect('/account/profile');
        }

        $phone = $req->input('phone');
        $this->users->updateProfile(
            $user->id,
            (string) $req->input('full_name'),
            ($phone !== null && $phone !== '') ? (string) $phone : null
        );

        Session::flash('success', 'Cập nhật hồ sơ thành công.');
        Response::redirect('/account/profile');
    }

    public function password(Request $req): void
    {
        View::render('shop/account/password', [
            'title'    => 'Đổi mật khẩu',
            'extraCss' => ['/assets/css/shop/account.css'],
        ]);
    }

    public function updatePassword(Request $req): void
    {
        Csrf::verify($req);
        $user = $this->auth->currentUser();
        if ($user === null) {
            Response::redirect('/login');
        }

        $v = (new Validator($req->all()))
            ->required('current_password', 'Mật khẩu hiện tại')
            ->required('new_password', 'Mật khẩu mới')->minLength('new_password', 8, 'Mật khẩu mới')
            ->required('new_password_confirmation', 'Xác nhận mật khẩu mới')->same('new_password_confirmation', 'new_password', 'Xác nhận mật khẩu mới');

        if (!$v->passes()) {
            Session::flash('errors', $v->errors());
            Response::redirect('/account/password');
        }

        $changed = $this->auth->changePassword(
            $user->id,
            (string) $req->input('current_password'),
            (string) $req->input('new_password')
        );

        if (!$changed) {
            Session::flash('error', 'Mật khẩu hiện tại không chính xác.');
            Response::redirect('/account/password');
        }

        Session::flash('success', 'Đổi mật khẩu thành công.');
        Response::redirect('/account/password');
    }
}

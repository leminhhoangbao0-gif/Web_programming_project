<?php
declare(strict_types=1);

namespace Atlas\Controllers\Shop;

use Atlas\Core\Csrf;
use Atlas\Core\Request;
use Atlas\Core\Response;
use Atlas\Core\Session;
use Atlas\Core\Validator;
use Atlas\Core\View;
use Atlas\Repositories\AddressRepository;
use Atlas\Services\AuthService;

final class AddressController
{
    private AuthService $auth;
    private AddressRepository $addresses;

    public function __construct()
    {
        $this->auth      = new AuthService();
        $this->addresses = new AddressRepository();
    }

    public function index(Request $req): void
    {
        $user = $this->auth->currentUser();
        if ($user === null) {
            Response::redirect('/login');
        }

        $editId  = (int) $req->query('edit', 0);
        $editing = $editId > 0 ? $this->addresses->findForUser($editId, $user->id) : null;

        View::render('shop/account/addresses', [
            'title'     => 'Sổ địa chỉ',
            'addresses' => $this->addresses->listByUser($user->id),
            'editing'   => $editing,
            'extraCss'  => ['/assets/css/shop/account.css'],
        ]);
    }

    public function store(Request $req): void
    {
        Csrf::verify($req);
        $user = $this->auth->currentUser();
        if ($user === null) {
            Response::redirect('/login');
        }

        $v = $this->validateAddress($req->all());
        if (!$v->passes()) {
            Session::flash('errors', $v->errors());
            Session::flash('old', $req->all());
            Response::redirect('/account/addresses');
        }

        $data               = $v->validated();
        $data['is_default'] = $req->input('is_default') === '1';

        $this->addresses->create($user->id, $data);

        Session::flash('success', 'Đã thêm địa chỉ mới.');
        Response::redirect('/account/addresses');
    }

    public function update(Request $req): void
    {
        Csrf::verify($req);
        $user = $this->auth->currentUser();
        if ($user === null) {
            Response::redirect('/login');
        }

        $addressId = (int) $req->param('id');
        if ($this->addresses->findForUser($addressId, $user->id) === null) {
            Response::notFound();
        }

        $v = $this->validateAddress($req->all());
        if (!$v->passes()) {
            Session::flash('errors', $v->errors());
            Response::redirect('/account/addresses?edit=' . $addressId);
        }

        $this->addresses->update($addressId, $user->id, $v->validated());

        Session::flash('success', 'Cập nhật địa chỉ thành công.');
        Response::redirect('/account/addresses');
    }

    public function destroy(Request $req): void
    {
        Csrf::verify($req);
        $user = $this->auth->currentUser();
        if ($user === null) {
            Response::redirect('/login');
        }

        $addressId = (int) $req->param('id');
        if ($this->addresses->findForUser($addressId, $user->id) === null) {
            Response::notFound();
        }

        $this->addresses->delete($addressId, $user->id);

        Session::flash('success', 'Đã xóa địa chỉ.');
        Response::redirect('/account/addresses');
    }

    public function setDefault(Request $req): void
    {
        Csrf::verify($req);
        $user = $this->auth->currentUser();
        if ($user === null) {
            Response::redirect('/login');
        }

        $addressId = (int) $req->param('id');
        if ($this->addresses->findForUser($addressId, $user->id) === null) {
            Response::notFound();
        }

        $this->addresses->setDefault($addressId, $user->id);

        Session::flash('success', 'Đã đặt làm địa chỉ mặc định.');
        Response::redirect('/account/addresses');
    }

    private function validateAddress(array $input): Validator
    {
        return (new Validator($input))
            ->required('recipient_name', 'Tên người nhận')->maxLength('recipient_name', 100, 'Tên người nhận')
            ->required('phone', 'Số điện thoại')->phoneVn('phone', 'Số điện thoại')
            ->required('address_line', 'Địa chỉ cụ thể')->maxLength('address_line', 255, 'Địa chỉ cụ thể')
            ->maxLength('ward', 100, 'Phường/Xã')
            ->maxLength('district', 100, 'Quận/Huyện')
            ->required('city', 'Tỉnh/Thành phố')->maxLength('city', 100, 'Tỉnh/Thành phố');
    }
}

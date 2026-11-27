<?php
namespace Atlas\Controllers\Shop;

use Atlas\Core\{Csrf, Request, Response, Session, Validator, View};
use Atlas\Repositories\AddressRepository;
use Atlas\Services\{AuthService, CheckoutService, PaymentService};

final class CheckoutController
{
    public function index(Request $r): void
    {
        $summary = (new CheckoutService())->summary((new AuthService())->currentUser()->id);
        View::render('shop/checkout/index', [
            'title' => 'Checkout', 'page' => 'checkout', 's' => $summary, 'methods' => (new PaymentService())->methods(),
        ]);
    }

    public function store(Request $r): void
    {
        Csrf::verify($r);
        $userId = (new AuthService())->currentUser()->id;
        $addresses = new AddressRepository();

        $addressId = (string)$r->input('address_id', 'new');
        if ($addressId !== 'new') {
            $address = $addresses->findForUser((int)$addressId, $userId);
            if (!$address) back_with_errors('/checkout', ['address_id' => 'Please choose an address.'], $r->all());
        } else {
            $v = AddressController::rules($r->all());
            if (!$v->passes()) back_with_errors('/checkout', $v->errors(), $r->all());
            $data = $v->validated();
            $id = $addresses->create($userId, $data);
            $address = $addresses->findForUser($id, $userId);
            if (!$r->input('save_address')) $addresses->delete($id, $userId);
        }

        $v = (new Validator($r->all()))->maxLength('customer_note', 500, 'Note')->required('payment_method', 'Payment method');
        if (!$v->passes()) back_with_errors('/checkout', $v->errors(), $r->all());

        try {
            $order = (new CheckoutService())->placeOrder($userId, [
                'ship_name' => $address->recipientName, 'ship_phone' => $address->phone, 'ship_address' => $address->fullText(),
                'delivery_option' => 'standard', 'customer_note' => $r->input('customer_note'),
            ], (string)$r->input('payment_method'));
        } catch (\DomainException $e) {
            Session::flash('error', error_message($e->getMessage()));
            Response::redirect($e->getMessage() === 'INSUFFICIENT_STOCK' ? '/cart' : '/checkout');
        }
        Response::redirect($order->paymentMethod === 'cod' ? "/order/success/{$order->orderCode}" : "/payment/{$order->orderCode}");
    }
}

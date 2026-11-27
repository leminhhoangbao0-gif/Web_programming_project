<?php
namespace Atlas\Controllers\Shop;

use Atlas\Core\{Csrf, Request, Response, Session, View};
use Atlas\Repositories\OrderRepository;
use Atlas\Services\{AuthService, PaymentService};

final class PaymentController
{
    private function order(Request $r)
    {
        return (new OrderRepository())->findForUser((string)$r->param('code'), (new AuthService())->currentUser()->id) ?? Response::notFound();
    }

    public function show(Request $r): void
    {
        $order = $this->order($r);
        if ($order->paymentMethod === 'cod' || $order->paymentStatus === 'paid') Response::redirect("/order/success/{$order->orderCode}");
        View::render('shop/payment/show', ['title' => 'Payment', 'order' => $order, 'methods' => (new PaymentService())->methods()]);
    }

    public function simulate(Request $r): void
    {
        Csrf::verify($r);
        $order = $this->order($r);
        try {
            $payment = (new PaymentService())->simulate($order, $r->input('outcome') === 'success' ? 'success' : 'failed');
        } catch (\DomainException $e) {
            Session::flash('error', error_message($e->getMessage()));
            Response::redirect("/order/success/{$order->orderCode}");
        }
        if ($payment->status === 'success') {
            Session::flash('success', 'Payment successful. Ref: ' . $payment->transactionRef);
            Response::redirect("/order/success/{$order->orderCode}");
        }
        Session::flash('error', 'Payment failed. You can try again.');
        Response::redirect("/payment/{$order->orderCode}");
    }

    public function success(Request $r): void
    {
        View::render('shop/payment/success', ['title' => 'Order placed', 'order' => $this->order($r), 'methods' => (new PaymentService())->methods()]);
    }
}

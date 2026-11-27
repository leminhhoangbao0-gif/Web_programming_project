<?php
namespace Atlas\Controllers\Shop;

use Atlas\Core\{Csrf, Paginator, Request, Response, Session, View};
use Atlas\Repositories\OrderRepository;
use Atlas\Services\{AuthService, OrderService};

final class OrderHistoryController
{
    public function index(Request $r): void
    {
        $page = max(1, (int)$r->query('page', 1));
        $result = (new OrderRepository())->listByUser((new AuthService())->currentUser()->id, $page);
        View::render('shop/orders/index', [
            'title' => 'My orders', 'orders' => $result['items'], 'paginator' => new Paginator($result['total'], $page, 10),
        ]);
    }

    public function show(Request $r): void
    {
        $repo = new OrderRepository();
        $order = $repo->findForUser((string)$r->param('code'), (new AuthService())->currentUser()->id) ?? Response::notFound();
        View::render('shop/orders/show', ['title' => 'Order ' . $order->orderCode, 'order' => $order, 'history' => $repo->history($order->id)]);
    }

    public function cancel(Request $r): void
    {
        Csrf::verify($r);
        $code = (string)$r->param('code');
        try {
            (new OrderService())->cancelByCustomer($code, (new AuthService())->currentUser()->id);
            Session::flash('success', 'Your order was cancelled.');
        } catch (\DomainException $e) {
            Session::flash('error', error_message($e->getMessage()));
        }
        Response::redirect('/account/orders/' . rawurlencode($code));
    }
}

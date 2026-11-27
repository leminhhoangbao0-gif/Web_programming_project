(function () {
    const KEY = 'atlas_cart_v1';
    const body = document.body;
    const isAuth = body.dataset.auth === '1';
    const base = body.dataset.base || '';
    const csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    const money = n => new Intl.NumberFormat('vi-VN').format(n) + ' ₫';
    const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    function readLocal() { try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { return []; } }
    function writeLocal(items) { try { localStorage.setItem(KEY, JSON.stringify(items)); } catch (e) {} }

    async function api(path, payload) {
        const res = await fetch(base + path, {
            method: payload === undefined ? 'GET' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-Token': csrf },
            body: payload === undefined ? undefined : JSON.stringify(payload),
            credentials: 'same-origin',
        });
        const json = await res.json();
        if (!json.ok) { const err = new Error(json.error.message); err.code = json.error.code; throw err; }
        return json.data;
    }

    let lastCount = 0;
    function setCount(cart) {
        lastCount = cart.count;
        document.querySelectorAll('[data-cart-count]').forEach(el => { el.textContent = cart.count; });
        return cart;
    }

    async function guestSync(items) {
        const cart = await api('/api/cart/preview', { items });
        writeLocal(cart.items.map(i => ({ product_id: i.product_id, quantity: i.quantity })));
        return setCount(cart);
    }

    const AtlasCart = {
        async add(productId, qty = 1) {
            if (isAuth) return setCount(await api('/api/cart/items', { product_id: productId, quantity: qty }));
            const items = readLocal();
            const found = items.find(i => i.product_id === productId);
            if (found) found.quantity += qty; else items.push({ product_id: productId, quantity: qty });
            return guestSync(items);
        },
        async update(productId, qty) {
            if (isAuth) return setCount(await api('/api/cart/items/update', { product_id: productId, quantity: qty }));
            const items = readLocal().map(i => i.product_id === productId ? { ...i, quantity: qty } : i).filter(i => i.quantity > 0);
            return guestSync(items);
        },
        async remove(productId) {
            if (isAuth) return setCount(await api('/api/cart/items/remove', { product_id: productId }));
            return guestSync(readLocal().filter(i => i.product_id !== productId));
        },
        async get() {
            if (isAuth) return setCount(await api('/api/cart'));
            const items = readLocal();
            if (!items.length) return setCount({ items: [], subtotal: 0, count: 0, notices: [] });
            return guestSync(items);
        },
        count() { return lastCount; },
        async mergeIfNeeded() {
            const items = readLocal();
            if (!isAuth || !items.length) return false;
            setCount(await api('/api/cart/merge', { items }));
            localStorage.removeItem(KEY);
            return true;
        },
    };
    window.AtlasCart = AtlasCart;

    function toast(msg, isError) {
        const el = document.getElementById('toast');
        if (!el) return alert(msg);
        el.textContent = msg;
        el.className = 'toast' + (isError ? ' toast-error' : '');
        el.hidden = false;
        clearTimeout(el._t);
        el._t = setTimeout(() => { el.hidden = true; }, 2500);
    }
    window.AtlasToast = toast;

    document.addEventListener('click', async e => {
        const btn = e.target.closest('[data-add-to-cart]');
        if (!btn) return;
        e.preventDefault();
        const input = btn.dataset.qtyInput ? document.querySelector(btn.dataset.qtyInput) : null;
        const qty = Math.max(1, parseInt(input ? input.value : '1', 10) || 1);
        btn.disabled = true;
        try {
            const cart = await AtlasCart.add(parseInt(btn.dataset.productId, 10), qty);
            toast(cart.notices.length ? cart.notices[0] : 'Added to cart ✓', cart.notices.length > 0);
        } catch (err) {
            toast(err.message, true);
        } finally {
            btn.disabled = false;
        }
    });

    const app = document.getElementById('cart-app');
    function renderCart(cart) {
        if (!app) return;
        const notices = cart.notices.map(n => `<div class="alert alert-error">${esc(n)}</div>`).join('');
        if (!cart.items.length) {
            app.innerHTML = notices + `<div class="empty"><p>Your cart is empty.</p><a class="btn btn-primary" href="${base}/products">Continue shopping</a></div>`;
            return;
        }
        const rows = cart.items.map(i => `
            <tr data-pid="${i.product_id}">
                <td class="cart-product"><img src="${esc(i.image_url)}" alt=""><a href="${base}/product/${esc(i.slug)}">${esc(i.name)}</a></td>
                <td>${money(i.unit_price)}</td>
                <td><input type="number" class="w-80" min="1" max="${i.stock}" value="${i.quantity}" data-cart-qty>
                    ${i.adjusted ? '<br><small class="neg">Reduced to stock</small>' : ''}</td>
                <td>${money(i.line_total)}</td>
                <td><button class="btn btn-danger" data-cart-remove>Remove</button></td>
            </tr>`).join('');
        app.innerHTML = notices + `
            <table class="table cart-table">
                <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
                <tbody>${rows}</tbody>
            </table>
            <div class="cart-footer card">
                <div class="summary-row total"><span>Subtotal</span><span>${money(cart.subtotal)}</span></div>
                <p class="muted">Shipping fee is added at checkout.</p>
                <a class="btn btn-primary btn-block" href="${esc(app.dataset.checkoutUrl)}">Proceed to checkout</a>
            </div>`;
    }
    if (app) {
        app.addEventListener('change', async e => {
            if (!e.target.matches('[data-cart-qty]')) return;
            const pid = parseInt(e.target.closest('tr').dataset.pid, 10);
            try { renderCart(await AtlasCart.update(pid, Math.max(1, parseInt(e.target.value, 10) || 1))); } catch (err) { toast(err.message, true); }
        });
        app.addEventListener('click', async e => {
            if (!e.target.matches('[data-cart-remove]')) return;
            const pid = parseInt(e.target.closest('tr').dataset.pid, 10);
            try { renderCart(await AtlasCart.remove(pid)); } catch (err) { toast(err.message, true); }
        });
    }

    const newAddr = document.querySelector('[data-new-address]');
    if (newAddr) {
        const sync = () => {
            const isNew = (document.querySelector('input[name="address_id"]:checked') || {}).value === 'new';
            newAddr.hidden = !isNew;
            newAddr.querySelectorAll('input[required], input[data-was-required]').forEach(inp => {
                if (isNew) { inp.required = true; } else { inp.dataset.wasRequired = '1'; inp.required = false; }
            });
        };
        document.querySelectorAll('input[name="address_id"]').forEach(r => r.addEventListener('change', sync));
        sync();
    }

    (async () => {
        try {
            const merged = await AtlasCart.mergeIfNeeded();
            if (merged && body.dataset.page === 'checkout') return location.reload();
            const cart = await AtlasCart.get();
            renderCart(cart);
        } catch (err) {
            if (app) app.innerHTML = `<div class="alert alert-error">${esc(err.message)}</div>`;
        }
    })();
})();

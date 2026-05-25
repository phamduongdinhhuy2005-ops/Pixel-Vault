<?php
$cart = $cart ?? [];
$cartTotal = $cartTotal ?? 0;
$itemsPerPage = 3;
$cartItems = $cart;
$totalItems = count($cartItems);
$totalQuantity = array_sum(array_map(fn($item) => (int) $item['quantity'], $cartItems));
$totalPages = max(1, (int) ceil($totalItems / $itemsPerPage));
$currentPage = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));
$visibleCartItems = array_slice($cartItems, ($currentPage - 1) * $itemsPerPage, $itemsPerPage, true);
$pageUrl = fn(int $page): string => url('Product/cart') . '?' . http_build_query(['page' => $page]);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - PIXEL VAULT</title>
    <meta name="description" content="Xem và quản lý giỏ hàng game retro của bạn tại Pixel Vault. Băng game cổ điển 8-bit chính hãng.">
    <link rel="icon" type="image/png" href="<?= url('uploads/pixel-vault-icon.png') ?>">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700&family=Space+Mono:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        :root { --ease-out-soft: cubic-bezier(.16,1,.3,1); --ease-snap: cubic-bezier(.2,.8,.2,1); }
        body { font-family: 'Be Vietnam Pro', sans-serif; line-height: 1.5; }
        .font-brand { font-family: 'Space Mono', monospace; }
        h1, h2, h3, .font-brand { line-height: 1.25; }
        button, a, .btn-press { line-height: 1.35; }

        .dot-matrix { background-image: radial-gradient(#cac6bc 1px, transparent 1px); background-size: 8px 8px; }

        .shadow-brutal    { box-shadow: 8px 8px 0px 0px #1b1c1c; }
        .shadow-brutal-sm { box-shadow: 4px 4px 0px 0px #1b1c1c; }
        .shadow-brutal-xs { box-shadow: 2px 2px 0px 0px #1b1c1c; }
        .shadow-inset     { box-shadow: inset 4px 4px 0 rgba(27,28,28,.22); }

        .btn-press { transition: transform .18s var(--ease-snap), box-shadow .18s var(--ease-snap), background-color .15s ease; }
        .btn-press:hover  { transform: translate(-2px,-3px); box-shadow: 8px 8px 0 #1b1c1c; }
        .btn-press:active { transform: translate(2px, 2px);  box-shadow: 2px 2px 0 #1b1c1c; }

        /* Neon glow on cart item image */
        @keyframes pv-neon-pulse {
            0%, 100% { box-shadow: 0 0 4px 0 #dbff5c, 0 0 12px 0 #dbff5c66, inset 0 0 4px 0 #dbff5c33; border-color: #dbff5c; }
            50%       { box-shadow: 0 0 8px 2px #dbff5c, 0 0 24px 4px #dbff5caa, inset 0 0 8px 0 #dbff5c55; border-color: #eaff7a; }
        }
        .neon-lime { animation: pv-neon-pulse 2.4s ease-in-out infinite; }

        /* Quantity input – remove browser spinners */
        input[type=number].qty-input::-webkit-inner-spin-button,
        input[type=number].qty-input::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number].qty-input { appearance: textfield; -moz-appearance: textfield; }

        /* Toast notification */
        #cart-toast {
            position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(120%);
            z-index: 9999; min-width: 260px; max-width: 90vw;
            transition: transform .25s cubic-bezier(.16,1,.3,1), opacity .25s ease;
            opacity: 0;
        }
        #cart-toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }

        input:focus-visible, select:focus-visible, textarea:focus-visible, button:focus-visible, a:focus-visible {
            outline: 3px solid #dbff5c;
            outline-offset: 3px;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#fbf9f8] text-[#1b1c1c] dot-matrix">
<?php $activePage = 'cart'; include __DIR__ . '/../shares/siteHeader.php'; ?>

<main class="mx-auto mb-20 w-full max-w-[1200px] flex-grow px-4 py-10 md:px-12">

    <!-- Page heading -->
    <div class="mb-10 flex flex-col gap-5 border-b-4 border-[#1b1c1c] pb-6 md:flex-row md:items-end md:justify-between">
        <h1 class="font-brand text-3xl font-bold uppercase leading-[1.22] md:text-5xl">
            <span class="block pb-2">Kho vật phẩm</span>
            <span class="block text-[#bb0509]">đã chọn</span>
        </h1>
        <span data-cart-count class="self-start bg-[#1b1c1c] px-4 py-2 font-brand text-xs font-bold uppercase text-white md:self-auto <?= $totalQuantity > 0 ? '' : 'hidden' ?>">
            Sức chứa: <?= $totalQuantity ?>/99
        </span>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="mb-6 border-4 border-[#1b1c1c] bg-[#dbff5c] px-4 py-3 font-bold text-[#3d4c00] shadow-brutal-sm">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="mb-6 border-4 border-[#bb0509] bg-white px-4 py-3 font-bold text-[#bb0509] shadow-brutal-sm">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <!-- Empty state -->
        <section class="border-4 border-[#1b1c1c] bg-[#f5f0e6] p-10 text-center shadow-brutal">
            <span class="material-symbols-outlined mb-4 block text-[#cac6bc]" style="font-size:72px">inventory_2</span>
            <h2 class="font-brand mb-3 text-2xl font-bold uppercase">Giỏ hàng đang trống</h2>
            <p class="mb-7 text-[#49473f]">Hãy thêm vài băng game vào kho trước khi bắt đầu nhiệm vụ thanh toán.</p>
            <a href="<?= url() ?>" class="btn-press inline-flex items-center gap-2 border-4 border-[#1b1c1c] bg-[#dbff5c] px-5 py-3 font-brand text-xs font-bold uppercase shadow-brutal-sm">
                <span class="material-symbols-outlined" style="font-size:18px">arrow_back</span>
                Tiếp tục mua sắm
            </a>
        </section>

    <?php else: ?>
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-12">

            <!-- Cart items -->
            <div class="flex flex-col gap-5 lg:col-span-7">
                <?php foreach ($visibleCartItems as $productId => $item): ?>
                    <?php
                    $quantity = (int) $item['quantity'];
                    $decreaseQuantity = max(0, $quantity - 1);
                    $increaseQuantity = $quantity + 1;
                    ?>
                    <article data-cart-row data-product-id="<?= (int) $productId ?>" data-unit-price="<?= htmlspecialchars((string) $item['price']) ?>"
                             class="relative flex flex-col gap-5 border-4 border-[#1b1c1c] bg-[#f5f0e6] p-4 shadow-brutal md:flex-row md:items-center">
                        <!-- Product image -->
                        <div class="neon-lime relative h-28 w-full shrink-0 overflow-hidden border-4 border-[#dbff5c] bg-white md:h-24 md:w-24">
                            <?php if ($item['image'] !== ''): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="h-full w-full object-contain p-2">
                            <?php else: ?>
                                <div class="flex h-full w-full items-center justify-center font-brand text-xl font-bold text-[#526600]">PV</div>
                            <?php endif; ?>
                        </div>

                        <!-- Info & controls -->
                        <div class="flex min-w-0 flex-1 flex-col gap-3">
                            <div>
                                <h2 class="font-brand text-lg font-bold uppercase leading-tight"><?= htmlspecialchars($item['name']) ?></h2>
                                <p class="mt-1 font-brand text-xs font-bold uppercase text-[#49473f]">Định dạng: băng game / Kho: Pixel Vault</p>
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <!-- Quantity stepper + direct input -->
                                <div class="flex items-center border-4 border-[#1b1c1c] bg-[#e4e2e1] shadow-brutal-sm">
                                    <form method="POST" action="<?= url('Product/updateCart/' . $productId) ?>" data-cart-quantity-form data-direction="decrease">
                                        <input type="hidden" name="quantity" value="<?= $decreaseQuantity ?>">
                                        <button type="submit" class="flex h-10 w-10 items-center justify-center border-r-4 border-[#1b1c1c] transition-colors hover:bg-[#dcd9d9] hover:text-[#bb0509]" aria-label="Giảm số lượng">
                                            <span class="material-symbols-outlined">remove</span>
                                        </button>
                                    </form>
                                    <input
                                        type="number"
                                        data-cart-quantity-input
                                        data-product-id="<?= (int) $productId ?>"
                                        value="<?= $quantity ?>"
                                        min="1" max="99"
                                        class="qty-input flex h-10 w-14 items-center justify-center bg-white text-center font-brand text-lg font-bold shadow-inset border-0 focus:ring-2 focus:ring-[#bb0509] focus:outline-none"
                                        aria-label="Số lượng sản phẩm"
                                    >
                                    <form method="POST" action="<?= url('Product/updateCart/' . $productId) ?>" data-cart-quantity-form data-direction="increase">
                                        <input type="hidden" name="quantity" value="<?= $increaseQuantity ?>">
                                        <button type="submit" class="flex h-10 w-10 items-center justify-center border-l-4 border-[#1b1c1c] transition-colors hover:bg-[#dcd9d9] hover:text-[#bb0509]" aria-label="Tăng số lượng">
                                            <span class="material-symbols-outlined">add</span>
                                        </button>
                                    </form>
                                </div>

                                <!-- Price + remove -->
                                <div class="flex items-center gap-5">
                                    <span data-item-total class="font-brand text-lg font-bold"><?= number_format($item['price'] * $quantity, 0, ',', '.') ?> <span class="text-sm">đ</span></span>
                                    <a data-cart-remove href="<?= url('Product/removeFromCart/' . $productId) ?>"
                                       class="btn-press flex h-11 w-11 items-center justify-center border-[3px] border-[#1b1c1c] bg-white text-[#49473f] shadow-brutal-sm hover:bg-[#ffdad6] hover:text-[#ba1a1a]"
                                       aria-label="Xóa sản phẩm">
                                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">delete</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4 flex flex-wrap items-center justify-center gap-2" aria-label="Phân trang giỏ hàng">
                        <a href="<?= htmlspecialchars($pageUrl(max(1, $currentPage - 1))) ?>"
                           class="inline-flex h-10 min-w-10 items-center justify-center border-4 border-[#1b1c1c] bg-[#fbf9f8] px-3 font-brand text-xs font-bold uppercase shadow-brutal-sm transition-all hover:bg-[#e4e2e1] <?= $currentPage <= 1 ? 'pointer-events-none opacity-40' : '' ?>">←</a>
                        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                            <a href="<?= htmlspecialchars($pageUrl($page)) ?>"
                               class="inline-flex h-10 min-w-10 items-center justify-center border-4 border-[#1b1c1c] px-3 font-brand text-sm font-bold shadow-brutal-sm transition-all <?= $page === $currentPage ? 'bg-[#bb0509] text-white' : 'bg-[#fbf9f8] hover:bg-[#dbff5c]' ?>">
                                <?= $page ?>
                            </a>
                        <?php endfor; ?>
                        <a href="<?= htmlspecialchars($pageUrl(min($totalPages, $currentPage + 1))) ?>"
                           class="inline-flex h-10 min-w-10 items-center justify-center border-4 border-[#1b1c1c] bg-[#fbf9f8] px-3 font-brand text-xs font-bold uppercase shadow-brutal-sm transition-all hover:bg-[#e4e2e1] <?= $currentPage >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>">→</a>
                    </nav>
                <?php endif; ?>
            </div>

            <!-- Order summary sidebar -->
            <aside class="relative h-fit lg:sticky lg:top-32 lg:col-span-5">
                <div class="relative z-20 flex flex-col border-4 border-[#1b1c1c] bg-[#f5f0e6] shadow-brutal">
                    <!-- Sidebar header -->
                    <div class="border-b-4 border-[#1b1c1c] bg-[#dbff5c] p-4">
                        <h2 class="text-center font-brand text-xl font-bold uppercase tracking-widest text-[#3d4c00]">Bảng dữ liệu</h2>
                    </div>

                    <div class="flex flex-col gap-7 p-6 md:p-8">
                        <!-- Subtotals -->
                        <div class="flex flex-col gap-5 border-b-4 border-dashed border-[#7a776e] pb-7">
                            <div class="flex justify-between text-base">
                                <span data-subtotal-label>Tạm tính (<?= $totalQuantity ?> vật phẩm)</span>
                                <strong data-cart-total><?= number_format($cartTotal, 0, ',', '.') ?> đ</strong>
                            </div>
                            <div class="flex justify-between text-base">
                                <span>Giao hàng</span>
                                <strong>Miễn phí</strong>
                            </div>
                        </div>

                        <!-- Promo code -->
                        <div class="flex flex-col gap-2">
                            <label class="font-brand text-xs font-bold uppercase text-[#49473f]">Mã ưu đãi</label>
                            <div class="flex gap-3">
                                <input class="w-full border-4 border-[#1b1c1c] bg-[#e4e2e1] p-3 font-brand text-sm uppercase shadow-inset focus:border-[#bb0509] focus:ring-0" placeholder="Nhập mã">
                                <button class="btn-press border-4 border-[#1b1c1c] bg-[#e4e2e1] px-5 font-brand text-xl font-bold shadow-brutal-sm">&gt;</button>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="border-4 border-[#1b1c1c] bg-[#1b1c1c] p-4 text-white">
                            <div class="flex items-center justify-between font-brand text-xl font-bold">
                                <span>Tổng tiền:</span>
                                <span data-cart-total class="text-[#dbff5c]"><?= number_format($cartTotal, 0, ',', '.') ?> đ</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col gap-4">
                            <a href="<?= url('Product/checkout') ?>"
                               class="btn-press block border-[3px] border-[#1b1c1c] bg-[#bb0509] p-4 text-center font-brand text-xl font-bold uppercase text-white shadow-brutal-sm hover:bg-[#df2b21]">
                                Tiến hành thanh toán
                            </a>
                            <a href="<?= url() ?>"
                               class="btn-press block border-[3px] border-[#1b1c1c] bg-white p-3 text-center font-brand text-xs font-bold uppercase shadow-brutal-sm hover:bg-[#e4e2e1]">
                                Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../shares/siteFooter.php'; ?>

<!-- Toast notification -->
<div id="cart-toast" role="alert" aria-live="assertive" class="flex items-center gap-3 border-4 border-[#bb0509] bg-white px-5 py-3 font-brand text-sm font-bold text-[#bb0509] shadow-brutal-sm">
    <span class="material-symbols-outlined shrink-0" style="font-variation-settings:'FILL' 1;font-size:20px">error</span>
    <span id="cart-toast-msg"></span>
</div>

<script>
const currencyFormatter = new Intl.NumberFormat('vi-VN');
function formatCurrency(v) { return currencyFormatter.format(v) + ' đ'; }

/* ── Toast ── */
let toastTimer = null;
function showToast(msg) {
    const toast = document.getElementById('cart-toast');
    document.getElementById('cart-toast-msg').textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 3500);
}

/* ── Validate quantity value ── */
function validateQty(raw) {
    const n = Number(raw);
    if (!Number.isInteger(n) || n < 1 || n > 99 || String(raw).trim() === '') return null;
    return n;
}

/* ── Refresh totals ── */
function refreshCartTotals() {
    const rows = [...document.querySelectorAll('[data-cart-row]')];
    const total = rows.reduce((sum, row) => {
        const qty = Number(row.querySelector('[data-cart-quantity-input]')?.value || 0);
        return sum + qty * Number(row.dataset.unitPrice || 0);
    }, 0);
    const count = rows.reduce((sum, row) => sum + Number(row.querySelector('[data-cart-quantity-input]')?.value || 0), 0);

    document.querySelectorAll('[data-cart-total]').forEach(el => { el.textContent = formatCurrency(total); });

    const subtotalLabel = document.querySelector('[data-subtotal-label]');
    if (subtotalLabel) subtotalLabel.textContent = `Tạm tính (${count} vật phẩm)`;

    const badge = document.querySelector('[data-cart-count]');
    if (badge) { badge.textContent = `Sức chứa: ${count}/99`; badge.classList.toggle('hidden', count === 0); }

    if (rows.length === 0) window.location.href = '<?= url('Product/cart') ?>';
}

/* ── Update item total display ── */
function updateItemTotal(row, qty) {
    const price = Number(row.dataset.unitPrice || 0);
    const el = row.querySelector('[data-item-total]');
    if (el) el.innerHTML = `${formatCurrency(price * qty).replace(' đ', '')} <span class="text-sm">đ</span>`;
}

/* ── Sync stepper hidden-inputs to current qty ── */
function syncStepperInputs(row, qty) {
    const forms = row.querySelectorAll('[data-cart-quantity-form]');
    forms.forEach(f => {
        const hidden = f.querySelector('input[name="quantity"]');
        if (!hidden) return;
        if (f.dataset.direction === 'decrease') hidden.value = Math.max(0, qty - 1);
        if (f.dataset.direction === 'increase') hidden.value = qty + 1;
    });
}

/* ── Send quantity update via fetch ── */
async function sendQtyUpdate(url, qty) {
    const fd = new FormData();
    fd.append('quantity', qty);
    await fetch(url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'fetch' } });
}

/* ── Stepper buttons (+ / -) ── */
document.querySelectorAll('[data-cart-quantity-form]').forEach(form => {
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const row = form.closest('[data-cart-row]');
        const inputEl = row.querySelector('[data-cart-quantity-input]');
        const nextQty = Number(form.querySelector('input[name="quantity"]').value);

        if (nextQty <= 0) {
            await sendQtyUpdate(form.action, 0);
            row.remove();
            refreshCartTotals();
            return;
        }

        await sendQtyUpdate(form.action, nextQty);
        inputEl.value = nextQty;
        updateItemTotal(row, nextQty);
        syncStepperInputs(row, nextQty);
        refreshCartTotals();
    });
});

/* ── Direct quantity input (blur + Enter) ── */
document.querySelectorAll('[data-cart-quantity-input]').forEach(input => {
    const row = input.closest('[data-cart-row]');
    const productId = input.dataset.productId;
    const updateUrl = '<?= url('Product/updateCart/') ?>' + productId;

    async function applyInput() {
        const raw = input.value.trim();
        const qty = validateQty(raw);

        if (qty === null) {
            showToast('Số lượng phải là số nguyên từ 1 đến 99.');
            // Restore previous valid value
            input.value = input.dataset.lastValid ?? 1;
            return;
        }

        input.dataset.lastValid = qty;
        input.value = qty;
        await sendQtyUpdate(updateUrl, qty);
        updateItemTotal(row, qty);
        syncStepperInputs(row, qty);
        refreshCartTotals();
    }

    // Set initial lastValid
    input.dataset.lastValid = input.value;

    input.addEventListener('blur', applyInput);
    input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); input.blur(); } });

    // Live: reject non-numeric chars visually
    input.addEventListener('input', () => {
        const v = input.value;
        if (v !== '' && (!/^\d+$/.test(v) || Number(v) > 99)) {
            input.classList.add('ring-2', 'ring-[#bb0509]');
        } else {
            input.classList.remove('ring-2', 'ring-[#bb0509]');
        }
    });
});

/* ── Remove button ── */
document.querySelectorAll('[data-cart-remove]').forEach(link => {
    link.addEventListener('click', async e => {
        e.preventDefault();
        const row = link.closest('[data-cart-row]');
        await fetch(link.href, { headers: { 'X-Requested-With': 'fetch' } });
        row.remove();
        refreshCartTotals();
    });
});
</script>
</body>
</html>


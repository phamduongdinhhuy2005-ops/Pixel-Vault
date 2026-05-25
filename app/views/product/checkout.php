<?php
$cart = $cart ?? [];
$cartTotal = $cartTotal ?? 0;
$itemsPerPage = 4;
$checkoutItems = $cart;
$totalItems = count($checkoutItems);
$totalPages = max(1, (int) ceil($totalItems / $itemsPerPage));
$currentPage = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));
$visibleCheckoutItems = array_slice($checkoutItems, ($currentPage - 1) * $itemsPerPage, $itemsPerPage, true);
$pageUrl = fn(int $page): string => url('Product/checkout') . '?' . http_build_query(['page' => $page]);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - Pixel Vault</title>
    <link rel="icon" type="image/png" href="<?= url('uploads/pixel-vault-icon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700&family=Space+Mono:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <style>
        body { font-family: 'Be Vietnam Pro', sans-serif; line-height: 1.5; background-color: #fbf9f8; background-image: radial-gradient(#cac6bc 1px, transparent 1px); background-size: 8px 8px; }
        .font-brand { font-family: 'Space Mono', monospace; }
        h1, h2, h3, .font-brand { line-height: 1.25; }
        button, a { line-height: 1.35; }
        .retro-shadow { box-shadow: 8px 8px 0 #1b1c1c; }
        .retro-shadow-sm { box-shadow: 4px 4px 0 #1b1c1c; }
        .btn-press { transition: transform .1s ease, box-shadow .1s ease, background-color .15s ease; }
        .btn-press:active { transform: translate(4px, 4px); box-shadow: none; }
        .inset-shadow { box-shadow: inset 0 4px 0 rgba(27, 28, 28, .1); }
    </style>
</head>
<body class="min-h-screen text-[#1b1c1c]">
<?php $activePage = 'cart'; include __DIR__ . '/../shares/siteHeader.php'; ?>

<main class="relative mx-auto max-w-[1200px] px-4 py-12 md:px-12">
    <div class="relative mb-10 flex items-end justify-between border-b-4 border-[#1b1c1c] pb-5">
        <h1 class="pr-8 font-brand text-3xl font-bold uppercase leading-tight md:text-5xl">Thanh toán - tổng kết nhiệm vụ</h1>
        <div class="absolute -right-2 -top-5 rotate-[3deg] border-[3px] border-[#1b1c1c] bg-[#dbff5c] px-3 py-1 retro-shadow-sm md:right-5">
            <span class="font-brand text-xs font-bold uppercase text-[#3d4c00]">Cấp độ: Thanh toán</span>
        </div>
    </div>

    <?php if (!empty($checkoutErrors)): ?>
        <div class="mb-8 space-y-1 border-4 border-[#bb0509] bg-white px-4 py-3 font-bold text-[#bb0509] retro-shadow-sm">
            <?php foreach ($checkoutErrors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <div class="flex flex-col gap-8 lg:col-span-7">
            <section class="border-4 border-[#1b1c1c] bg-[#fbf9f8] p-6 retro-shadow md:p-8">
                <div class="mb-6 inline-flex items-center gap-2 border-[3px] border-[#1b1c1c] bg-[#1b1c1c] px-4 py-2 text-white">
                    <span class="material-symbols-outlined">person</span>
                    <h2 class="font-brand text-xl font-bold uppercase">Thông tin người nhận</h2>
                </div>

                <form id="checkout-form" method="POST" action="<?= url('Product/processCheckout') ?>" class="flex flex-col gap-6">
                    <label>
                        <span class="mb-2 block font-brand text-xs font-bold uppercase">Họ tên người nhận</span>
                        <input name="name" id="checkout-name" value="<?= htmlspecialchars($checkoutOld['name'] ?? '') ?>" class="w-full border-[3px] border-[#1b1c1c] bg-white px-4 py-3 text-[#1b1c1c] inset-shadow focus:border-[#bb0509] focus:ring-0" placeholder="Nhập họ tên..." required>
                    </label>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <label>
                            <span class="mb-2 block font-brand text-xs font-bold uppercase">Số điện thoại chính <span class="text-[#bb0509]">&ast;</span></span>
                            <input name="phone" id="checkout-phone" value="<?= htmlspecialchars($checkoutOld['phone'] ?? '') ?>" class="w-full border-[3px] border-[#1b1c1c] bg-white px-4 py-3 text-[#1b1c1c] inset-shadow focus:border-[#bb0509] focus:ring-0" placeholder="VD: 0901 234 567" required>
                        </label>
                        <label>
                            <span class="mb-2 block font-brand text-xs font-bold uppercase">Số điện thoại phụ <span class="text-[#49473f] font-normal">(không bắt buộc)</span></span>
                            <input name="phone2" id="checkout-phone2" value="<?= htmlspecialchars($checkoutOld['phone2'] ?? '') ?>" class="w-full border-[3px] border-[#1b1c1c] bg-white px-4 py-3 text-[#1b1c1c] inset-shadow focus:border-[#bb0509] focus:ring-0" placeholder="VD: 0912 345 678">
                        </label>
                    </div>
                    <label>
                        <span class="mb-2 block font-brand text-xs font-bold uppercase">Địa chỉ giao hàng</span>
                        <textarea name="address" id="checkout-address" rows="3" class="w-full border-[3px] border-[#1b1c1c] bg-white px-4 py-3 text-[#1b1c1c] inset-shadow focus:border-[#bb0509] focus:ring-0" placeholder="Nhập địa chỉ giao hàng..." required><?= htmlspecialchars($checkoutOld['address'] ?? '') ?></textarea>
                    </label>
                    <label>
                        <span class="mb-2 flex items-center gap-2 font-brand text-xs font-bold uppercase">
                            Ghi chú đơn hàng <span class="text-[#49473f] font-normal">(không bắt buộc)</span>
                        </span>
                        <textarea name="note" id="checkout-note" rows="3" class="w-full border-[3px] border-[#1b1c1c] bg-white px-4 py-3 text-[#1b1c1c] inset-shadow focus:border-[#bb0509] focus:ring-0" placeholder="Ghi chú giao hàng, yêu cầu đặc biệt (giao giờ hành chính, gõ nhẹ...)..."><?= htmlspecialchars($checkoutOld['note'] ?? '') ?></textarea>
                    </label>
                </form>
            </section>

            <section class="border-4 border-[#1b1c1c] bg-[#fbf9f8] p-6 retro-shadow md:p-8">
                <div class="mb-6 inline-flex items-center gap-2 border-[3px] border-[#1b1c1c] bg-[#1b1c1c] px-4 py-2 text-white">
                    <span class="material-symbols-outlined">payments</span>
                    <h2 class="font-brand text-xl font-bold uppercase">Phương thức thanh toán</h2>
                </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <?php foreach ([['payments', 'Thanh toán khi nhận'], ['credit_card', 'Thẻ nội địa'], ['account_balance_wallet', 'Ví điện tử']] as $index => [$icon, $label]): ?>
                        <label class="cursor-pointer">
                            <input <?= $index === 0 ? 'checked' : '' ?> class="peer sr-only" name="payment_method" type="radio" form="checkout-form" value="<?= htmlspecialchars($label) ?>">
                            <div class="flex h-full flex-col items-center gap-2 border-[3px] border-[#1b1c1c] bg-white p-4 text-center retro-shadow-sm transition-colors hover:bg-[#f5f0e6] peer-checked:border-[#bb0509] peer-checked:bg-[#ffdad5]">
                                <span class="material-symbols-outlined text-[32px]"><?= $icon ?></span>
                                <span class="mt-auto font-brand text-xs font-bold uppercase"><?= htmlspecialchars($label) ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="mt-6 border-t-2 border-[#1b1c1c] pt-5 text-sm font-semibold text-[#49473f]">Hiện tại hệ thống sẽ lưu đơn hàng và nhân viên Pixel Vault xác nhận thanh toán sau.</p>
            </section>
        </div>

        <div class="lg:col-span-5">
            <section class="sticky top-[100px] border-4 border-[#1b1c1c] bg-[#fbf9f8] retro-shadow">
                <div class="border-b-4 border-[#1b1c1c] bg-[#dbff5c] p-4 text-center">
                    <h2 class="font-brand text-xl font-bold uppercase tracking-widest text-[#3d4c00]">Tóm tắt nhiệm vụ</h2>
                </div>
                <div class="flex flex-col gap-4 p-6">
                    <?php foreach ($visibleCheckoutItems as $item): ?>
                        <div class="flex gap-4 border-b-2 border-[#1b1c1c] pb-4">
                            <div class="h-16 w-16 shrink-0 border-2 border-[#1b1c1c] bg-white bg-center bg-contain bg-no-repeat" style="<?= $item['image'] !== '' ? "background-image: url('" . htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') . "')" : '' ?>">
                                <?php if ($item['image'] === ''): ?>
                                    <span class="flex h-full w-full items-center justify-center font-brand text-xs font-bold text-[#526600]">PV</span>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="truncate font-brand text-xs font-bold uppercase"><?= htmlspecialchars($item['name']) ?></h3>
                                <p class="text-sm text-[#49473f]">Số lượng: <?= (int) $item['quantity'] ?></p>
                            </div>
                            <div class="text-right">
                                <span class="font-brand text-xs font-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if ($totalPages > 1): ?>
                        <nav class="flex flex-wrap items-center justify-center gap-2" aria-label="Phân trang tóm tắt đơn hàng">
                            <a href="<?= htmlspecialchars($pageUrl(max(1, $currentPage - 1))) ?>" class="inline-flex h-10 min-w-10 items-center justify-center border-4 border-[#1b1c1c] bg-[#fbf9f8] px-3 font-brand text-xs font-bold uppercase retro-shadow-sm transition-all hover:bg-[#e4e2e1] <?= $currentPage <= 1 ? 'pointer-events-none opacity-40' : '' ?>">←</a>
                            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                <a href="<?= htmlspecialchars($pageUrl($page)) ?>" class="inline-flex h-10 min-w-10 items-center justify-center border-4 border-[#1b1c1c] px-3 font-brand text-sm font-bold retro-shadow-sm transition-all <?= $page === $currentPage ? 'bg-[#bb0509] text-white' : 'bg-[#fbf9f8] hover:bg-[#e4e2e1]' ?>"><?= $page ?></a>
                            <?php endfor; ?>
                            <a href="<?= htmlspecialchars($pageUrl(min($totalPages, $currentPage + 1))) ?>" class="inline-flex h-10 min-w-10 items-center justify-center border-4 border-[#1b1c1c] bg-[#fbf9f8] px-3 font-brand text-xs font-bold uppercase retro-shadow-sm transition-all hover:bg-[#e4e2e1] <?= $currentPage >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>">→</a>
                        </nav>
                    <?php endif; ?>

                    <div class="mt-3 flex flex-col gap-2 border-t-[3px] border-[#1b1c1c] pt-4">
                        <div class="flex justify-between"><span>Tạm tính</span><span class="font-brand font-bold"><?= number_format($cartTotal, 0, ',', '.') ?> đ</span></div>
                        <div class="flex justify-between"><span>Giao hàng</span><span class="font-brand font-bold">0 đ</span></div>
                        <div class="mt-2 flex justify-between border-t-[3px] border-[#1b1c1c] pt-4">
                            <span class="font-brand text-xl font-bold uppercase">Tổng</span>
                            <span class="font-brand text-xl font-bold text-[#bb0509]"><?= number_format($cartTotal, 0, ',', '.') ?> đ</span>
                        </div>
                    </div>

                    <button form="checkout-form" class="btn-press mt-4 flex w-full items-center justify-center gap-2 border-[3px] border-[#1b1c1c] bg-[#bb0509] px-6 py-4 font-brand text-xl font-bold uppercase text-white retro-shadow hover:bg-[#df2b21]">
                        Xác nhận đặt hàng
                        <span class="material-symbols-outlined">play_arrow</span>
                    </button>
                    <a href="<?= url('Product/cart') ?>" class="text-center font-brand text-xs font-bold uppercase text-[#49473f] underline decoration-2 underline-offset-4">Quay lại giỏ hàng</a>
                </div>
            </section>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../shares/siteFooter.php'; ?>
</body>
</html>

<?php
$orderItems = is_array($orderItems ?? null) ? $orderItems : [];
$orderTotal = (float) ($orderTotal ?? 0);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công - Pixel Vault</title>
    <link rel="icon" type="image/png" href="<?= url('uploads/pixel-vault-icon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&family=Space+Mono:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <style>
        body { font-family: 'Be Vietnam Pro', sans-serif; background-color: #fbf9f8; background-image: radial-gradient(#cac6bc 1px, transparent 1px); background-size: 8px 8px; }
        .font-brand { font-family: 'Space Mono', monospace; }
        .hard-shadow-lg { box-shadow: 8px 8px 0 #1b1c1c; }
        .hard-shadow-md { box-shadow: 4px 4px 0 #1b1c1c; }
        .sunken-screen { box-shadow: inset 0 4px 0 rgba(27, 28, 28, .1); }
        .retro-btn { transition: transform .1s ease, box-shadow .1s ease, background-color .15s ease; }
        .retro-btn:active { transform: translate(4px, 4px); box-shadow: none; }
    </style>
</head>
<body class="flex min-h-screen flex-col text-[#1b1c1c]">
<?php $activePage = 'cart'; include __DIR__ . '/../shares/siteHeader.php'; ?>

<main class="relative mx-auto my-12 flex w-[calc(100%-32px)] max-w-[860px] flex-col overflow-hidden border-4 border-[#1b1c1c] bg-[#fbf9f8] hard-shadow-lg md:my-16">
    <div class="relative flex flex-col items-center justify-center border-b-4 border-[#1b1c1c] bg-[#f5f0e6] p-8 md:p-10">
        <div class="absolute left-2 top-2 flex h-3 w-3 items-center justify-center rounded-full border-2 border-[#1b1c1c]"><div class="h-[2px] w-2 rotate-45 bg-[#1b1c1c]"></div></div>
        <div class="absolute right-2 top-2 flex h-3 w-3 items-center justify-center rounded-full border-2 border-[#1b1c1c]"><div class="h-[2px] w-2 -rotate-45 bg-[#1b1c1c]"></div></div>
        <div class="mb-6 flex h-28 w-28 items-center justify-center border-4 border-[#1b1c1c] bg-white hard-shadow-md md:h-32 md:w-32">
            <span class="material-symbols-outlined text-[#bb0509]" style="font-size:82px;font-variation-settings:'FILL' 1">emoji_events</span>
        </div>
        <h1 class="text-center font-brand text-3xl font-bold uppercase text-[#1b1c1c] md:text-5xl">Nhiệm vụ hoàn tất!</h1>
    </div>

    <div class="flex flex-col gap-8 p-6 md:p-8">
        <div class="flex flex-col gap-2 text-center">
            <p class="inline-block self-center border-2 border-dashed border-[#bb0509] bg-[#ffdad5] px-3 py-1 font-brand text-sm font-bold uppercase text-[#bb0509]">
                Mã đơn hàng: <?= $orderId > 0 ? '#PV-' . str_pad((string) $orderId, 4, '0', STR_PAD_LEFT) : '#PV-ĐANG-CẬP-NHẬT' ?>
            </p>
            <p class="mt-2 text-lg leading-relaxed">Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đã được hệ thống Pixel Vault ghi nhận và đang chờ xử lý.</p>
        </div>

        <div class="flex flex-col border-4 border-[#1b1c1c] bg-[#fbf9f8] sunken-screen">
            <div class="flex items-center gap-2 border-b-4 border-[#1b1c1c] bg-[#1b1c1c] px-4 py-2">
                <span class="material-symbols-outlined text-[#dbff5c]" style="font-size:18px">receipt_long</span>
                <h2 class="font-brand text-xs font-bold uppercase text-white">Nhật ký đơn hàng</h2>
            </div>
            <div class="flex flex-col gap-4 p-4">
                <?php if (!empty($orderItems)): ?>
                    <ul class="flex flex-col">
                        <?php foreach ($orderItems as $item): ?>
                            <li class="flex items-center justify-between gap-4 border-b-2 border-dotted border-[#1b1c1c] py-3">
                                <span><?= (int) $item['quantity'] ?>x <?= htmlspecialchars($item['name']) ?></span>
                                <span class="font-brand text-lg font-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="flex items-center justify-between pt-2">
                        <span class="font-brand text-xs font-bold uppercase text-[#49473f]">Tổng thanh toán</span>
                        <span class="font-brand text-3xl font-bold text-[#bb0509]"><?= number_format($orderTotal, 0, ',', '.') ?> đ</span>
                    </div>
                <?php else: ?>
                    <p class="text-center text-[#49473f]">Thông tin tóm tắt đơn hàng đã được lưu trong cơ sở dữ liệu.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <div class="flex items-end justify-between">
                <h3 class="font-brand text-xs font-bold uppercase">Tiến trình giao hàng</h3>
                <span class="animate-pulse font-brand text-xs font-bold uppercase text-[#bb0509]">Đang khởi tạo...</span>
            </div>
            <div class="relative h-8 w-full overflow-hidden border-4 border-[#1b1c1c] bg-[#e4e2e1]">
                <div class="absolute left-0 top-0 h-full w-[18%] border-r-4 border-[#1b1c1c] bg-[#bb0509]"></div>
                <div class="absolute inset-0 opacity-50" style="background-image: linear-gradient(90deg, rgba(27,28,28,.18) 1px, transparent 1px); background-size: 8px 100%;"></div>
            </div>
        </div>
    </div>

    <div class="flex flex-col items-center gap-5 border-t-4 border-[#1b1c1c] bg-[#f5f0e6] p-6 md:flex-row md:justify-between md:p-8">
        <a href="<?= url('Product/cart') ?>" class="retro-btn w-full -skew-x-[15deg] border-4 border-[#1b1c1c] bg-white px-6 py-4 text-center font-brand text-xs font-bold uppercase text-[#49473f] hard-shadow-md hover:bg-[#e4e2e1] md:w-auto">
            <span class="inline-flex skew-x-[15deg] items-center justify-center gap-2"><span class="material-symbols-outlined" style="font-size:16px">history</span>Xem giỏ hàng</span>
        </a>
        <a href="<?= url() ?>" class="retro-btn flex w-full items-center justify-center gap-2 border-4 border-[#1b1c1c] bg-[#bb0509] px-6 py-4 font-brand text-xl font-bold uppercase text-white hard-shadow-md hover:bg-[#df2b21] md:w-auto">
            Tiếp tục mua sắm
            <span class="material-symbols-outlined">play_arrow</span>
        </a>
    </div>

    <div class="pointer-events-none absolute bottom-2 left-0 z-30 w-full text-center">
        <span class="font-brand text-xs font-bold uppercase tracking-widest text-[#49473f]/50">Điểm thưởng: 10.000 pts | Nhấn tiếp tục để quay lại cửa hàng</span>
    </div>
</main>

<?php include __DIR__ . '/../shares/siteFooter.php'; ?>
</body>
</html>

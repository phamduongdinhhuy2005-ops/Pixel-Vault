<?php
$orders = $orders ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đơn hàng – PIXEL VAULT</title>
    <meta name="description" content="Xem toàn bộ danh sách đơn hàng đã đặt tại Pixel Vault.">
    <link rel="icon" type="image/png" href="<?= url('uploads/pixel-vault-icon.png') ?>">
    <link rel="apple-touch-icon" href="<?= url('uploads/pixel-vault-icon.png') ?>">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700&family=Space+Mono:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        html {
            font-size: 88%;
        }
        @media (max-width: 640px) {
            html {
                font-size: 92%;
            }
        }
        body { font-family: 'Be Vietnam Pro', sans-serif; line-height: 1.5; }
        .font-brand { font-family: 'Space Mono', monospace; }
        h1, h2, h3, .font-brand { line-height: 1.25; }
        button, a, .btn-press { line-height: 1.35; }

        /* Light dot-matrix for admin pages */
        .dot-matrix {
            background-color: #fbf9f8;
            background-image: radial-gradient(#cac6bc 1px, transparent 1px);
            background-size: 8px 8px;
        }
        .scanlines {
            background: linear-gradient(to bottom, rgba(255,255,255,0) 50%, rgba(0,0,0,0.04) 50%);
            background-size: 100% 4px;
            pointer-events: none;
        }

        .btn-press { transition: transform .12s ease, box-shadow .12s ease; }
        .btn-press:hover  { transform: translate(-2px,-2px); }
        .btn-press:active { transform: translate(2px, 2px); }

        .nav-link { transition: transform .12s ease; }
        .nav-link:hover { transform: translate(1px,-1px); }

        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }
        .blink { animation: blink 1s step-end infinite; }

        @keyframes ticker { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
        .ticker-track { animation: ticker 30s linear infinite; white-space: nowrap; }

        /* Table rows */
        .order-row { transition: background .12s ease; }
        .order-row:hover { background: rgba(187,5,9,0.04); }

        /* Expandable note / address */
        .truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Mobile card */
        @media (max-width: 1023px) {
            .desktop-table { display: none; }
            .mobile-cards  { display: flex; }
        }
        @media (min-width: 1024px) {
            .desktop-table { display: block; }
            .mobile-cards  { display: none; }
        }

        input:focus-visible, button:focus-visible, a:focus-visible {
            outline: 3px solid #dbff5c;
            outline-offset: 3px;
        }
    </style>
</head>
<body class="dot-matrix text-[#1b1c1c] min-h-screen">

<!-- ─── TOP NAV ─── -->
<nav class="bg-[#fbf9f8] border-b-4 border-[#1b1c1c] shadow-[0px_4px_0px_0px_#1b1c1c] sticky top-0 z-50">
    <!-- Ticker -->
    <div class="bg-[#bb0509] text-white overflow-hidden py-1.5">
        <div class="ticker-track inline-block font-brand text-[9px] font-bold uppercase tracking-[.15em]">
            &nbsp;&nbsp;⚡ PIXEL VAULT ADMIN &nbsp;·&nbsp; DANH SÁCH ĐƠN HÀNG &nbsp;·&nbsp; <?= count($orders) ?> ĐƠN HÀNG &nbsp;·&nbsp; HỆ THỐNG ĐANG HOẠT ĐỘNG &nbsp;&nbsp;⚡ PIXEL VAULT ADMIN &nbsp;·&nbsp; DANH SÁCH ĐƠN HÀNG &nbsp;·&nbsp; <?= count($orders) ?> ĐƠN HÀNG &nbsp;·&nbsp; HỆ THỐNG ĐANG HOẠT ĐỘNG &nbsp;&nbsp;
        </div>
    </div>

    <!-- Main row -->
    <div class="max-w-[1400px] mx-auto px-4 md:px-12 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="<?= url() ?>" class="font-brand text-xl font-bold italic text-[#bb0509] tracking-tighter">PIXEL_VAULT</a>
            <div class="hidden md:flex items-center gap-2 border-l-2 border-[#cac6bc] pl-4">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[10px] font-bold uppercase tracking-[.12em] text-[#526600]">HỆ THỐNG HOẠT ĐỘNG</span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <a href="<?= url('Admin') ?>" class="nav-link flex items-center gap-2 text-[11px] font-bold uppercase tracking-[.12em] text-[#49473f] hover:text-[#bb0509] transition-colors">
                <span class="material-symbols-outlined" style="font-size:16px">dashboard</span>
                Quản Trị
            </a>
            <a href="<?= url() ?>" class="nav-link flex items-center gap-2 text-[11px] font-bold uppercase tracking-[.12em] text-[#49473f] hover:text-[#bb0509] transition-colors">
                <span class="material-symbols-outlined" style="font-size:16px">storefront</span>
                Cửa Hàng
            </a>
        </div>
    </div>
</nav>

<main class="max-w-[1400px] mx-auto px-4 md:px-12 py-8 space-y-8">

    <!-- ─── PAGE HEADER ─── -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <p class="font-brand text-[10px] text-[#526600] font-bold uppercase tracking-[.15em] mb-2">
                > ĐĂNG NHẬP: QUẢN_TRỊ_VIÊN<span class="blink">_</span>
            </p>
            <h1 class="font-brand text-3xl md:text-4xl font-bold text-[#1b1c1c] uppercase tracking-tight">Danh sách đơn hàng</h1>
            <p class="text-sm text-[#49473f] mt-1">PIXEL VAULT — Quản Lý Đơn Hàng Khách Đặt</p>
        </div>
        <div class="flex items-center gap-2 bg-[#fbf9f8] border-4 border-[#1b1c1c] px-5 py-3 shadow-[4px_4px_0_#bb0509]">
            <span class="material-symbols-outlined text-[#bb0509]" style="font-variation-settings:'FILL' 1;font-size:20px">receipt_long</span>
            <span class="font-brand text-[11px] font-bold uppercase tracking-[.12em] text-[#1b1c1c]"><?= count($orders) ?> đơn hàng</span>
        </div>
    </div>

    <!-- ─── SUMMARY STATS ─── -->
    <?php
    $totalRevenue = array_sum(array_map(fn($o) => (float) $o['total_amount'], $orders));
    $totalItems   = array_sum(array_map(fn($o) => (int) $o['item_count'], $orders));
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white border-4 border-[#1b1c1c] p-6 relative overflow-hidden" style="box-shadow:6px 6px 0 #bb0509">
            <div class="absolute inset-0 scanlines opacity-15"></div>
            <div class="absolute top-0 left-0 right-0 h-1 bg-[#bb0509]"></div>
            <p class="text-[10px] font-bold uppercase tracking-[.15em] text-[#bb0509] mb-2">TỔNG ĐƠN HÀNG</p>
            <p class="font-brand text-5xl font-bold text-[#1b1c1c]"><?= count($orders) ?></p>
            <p class="text-[10px] text-[#49473f] mt-2 uppercase tracking-widest">đơn đã ghi nhận</p>
            <span class="material-symbols-outlined absolute bottom-4 right-4 text-[#bb0509]/10" style="font-size:72px;font-variation-settings:'FILL' 1">shopping_bag</span>
        </div>
        <div class="bg-white border-4 border-[#1b1c1c] p-6 relative overflow-hidden" style="box-shadow:6px 6px 0 #526600">
            <div class="absolute inset-0 scanlines opacity-15"></div>
            <div class="absolute top-0 left-0 right-0 h-1 bg-[#526600]"></div>
            <p class="text-[10px] font-bold uppercase tracking-[.15em] text-[#526600] mb-2">TỔNG DOANH THU</p>
            <p class="font-brand text-3xl font-bold text-[#526600]"><?= number_format($totalRevenue, 0, ',', '.') ?>đ</p>
            <p class="text-[10px] text-[#49473f] mt-2 uppercase tracking-widest">từ tất cả đơn hàng</p>
            <span class="material-symbols-outlined absolute bottom-4 right-4 text-[#526600]/10" style="font-size:72px;font-variation-settings:'FILL' 1">payments</span>
        </div>
        <div class="bg-white border-4 border-[#1b1c1c] p-6 relative overflow-hidden" style="box-shadow:6px 6px 0 #1b1c1c">
            <div class="absolute inset-0 scanlines opacity-15"></div>
            <div class="absolute top-0 left-0 right-0 h-1 bg-[#1b1c1c]"></div>
            <p class="text-[10px] font-bold uppercase tracking-[.15em] text-[#1b1c1c] font-bold mb-2">TỔNG SẢN PHẨM</p>
            <p class="font-brand text-3xl font-bold text-[#1b1c1c]"><?= $totalItems ?></p>
            <p class="text-[10px] text-[#49473f] mt-2 uppercase tracking-widest">mặt hàng đã bán</p>
            <span class="material-symbols-outlined absolute bottom-4 right-4 text-[#1b1c1c]/10" style="font-size:72px;font-variation-settings:'FILL' 1">inventory_2</span>
        </div>
    </div>

    <!-- ─── EMPTY STATE ─── -->
    <?php if (empty($orders)): ?>
    <div class="bg-white border-4 border-[#1b1c1c] shadow-[8px_8px_0_#1b1c1c] p-16 text-center">
        <span class="material-symbols-outlined text-[#cac6bc] block mb-4" style="font-size:80px;font-variation-settings:'FILL' 1">receipt_long</span>
        <p class="font-brand text-xl font-bold uppercase text-[#1b1c1c] mb-2">Chưa có đơn hàng nào</p>
        <p class="text-sm text-[#49473f]">Khi khách hàng đặt hàng, các đơn sẽ xuất hiện tại đây.</p>
        <a href="<?= url() ?>" class="inline-flex items-center gap-2 mt-6 bg-[#bb0509] text-white px-6 py-3 border-4 border-[#1b1c1c] shadow-[4px_4px_0_#1b1c1c] text-[11px] font-bold uppercase tracking-[.12em] hover:brightness-110 transition-all">
            <span class="material-symbols-outlined" style="font-size:16px">storefront</span>
            Về cửa hàng
        </a>
    </div>

    <?php else: ?>

    <!-- ─── DESKTOP TABLE ─── -->
    <div class="desktop-table bg-white border-4 border-[#1b1c1c] shadow-[8px_8px_0px_0px_#1b1c1c]">
        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-[#1b1c1c] bg-[#eae8e7]">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#bb0509]" style="font-variation-settings:'FILL' 1">table_view</span>
                <span class="text-[#1b1c1c] text-[11px] font-bold uppercase tracking-[.12em]">BẢNG ĐƠN HÀNG</span>
            </div>
            <span class="bg-[#bb0509] text-white text-[10px] font-bold px-3 py-1 font-brand"><?= count($orders) ?> đơn</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-[#1b1c1c] bg-[#fbf9f8]">
                        <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f] whitespace-nowrap">Mã đơn</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f]">Tên khách</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f] whitespace-nowrap">SĐT chính</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f] whitespace-nowrap">SĐT phụ</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f]">Địa chỉ</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f]">Ghi chú</th>
                        <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f] whitespace-nowrap">Tổng tiền</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f] whitespace-nowrap">Ngày đặt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eae8e7] bg-white">
                    <?php foreach ($orders as $order): ?>
                    <?php
                        $orderId   = (int) $order['id'];
                        $orderCode = '#PV-' . str_pad((string) $orderId, 4, '0', STR_PAD_LEFT);
                        $createdAt = $order['created_at'] ? date('d/m/Y H:i', strtotime($order['created_at'])) : '—';
                        $total     = (float) $order['total_amount'];
                    ?>
                    <tr class="order-row">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-brand text-[#bb0509] text-xs font-bold"><?= htmlspecialchars($orderCode) ?></span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="font-semibold text-[#1b1c1c]"><?= htmlspecialchars($order['name']) ?></span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="font-brand text-[#526600] text-xs font-bold"><?= htmlspecialchars($order['phone']) ?></span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <?php if (!empty($order['phone2'])): ?>
                                <span class="font-brand text-[#49473f] text-xs"><?= htmlspecialchars($order['phone2']) ?></span>
                            <?php else: ?>
                                <span class="text-[#cac6bc] text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 max-w-[220px]">
                            <span class="truncate-2 text-[#1b1c1c] text-sm"><?= htmlspecialchars($order['address']) ?></span>
                        </td>
                        <td class="px-4 py-4 max-w-[180px]">
                            <?php if (!empty($order['note'])): ?>
                                <span class="truncate-2 text-[#49473f] text-sm italic"><?= htmlspecialchars($order['note']) ?></span>
                            <?php else: ?>
                                <span class="text-[#cac6bc] text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap">
                            <span class="font-brand font-bold text-[#bb0509]"><?= number_format($total, 0, ',', '.') ?>đ</span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="text-[#49473f] text-xs"><?= $createdAt ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── MOBILE CARDS ─── -->
    <div class="mobile-cards flex-col gap-4">
        <?php foreach ($orders as $order): ?>
        <?php
            $orderId   = (int) $order['id'];
            $orderCode = '#PV-' . str_pad((string) $orderId, 4, '0', STR_PAD_LEFT);
            $createdAt = $order['created_at'] ? date('d/m/Y H:i', strtotime($order['created_at'])) : '—';
            $total     = (float) $order['total_amount'];
        ?>
        <div class="bg-white border-4 border-[#1b1c1c] shadow-[4px_4px_0_#1b1c1c]">
            <!-- Card header -->
            <div class="flex items-center justify-between border-b-4 border-[#1b1c1c] bg-[#eae8e7] px-4 py-3">
                <span class="font-brand text-[#bb0509] text-sm font-bold"><?= htmlspecialchars($orderCode) ?></span>
                <span class="font-brand text-[#bb0509] text-sm font-bold"><?= number_format($total, 0, ',', '.') ?>đ</span>
            </div>
            <!-- Card body -->
            <div class="p-4 space-y-3 text-sm">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#49473f] shrink-0" style="font-size:16px">person</span>
                    <span class="text-[#1b1c1c] font-semibold"><?= htmlspecialchars($order['name']) ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#49473f] shrink-0" style="font-size:16px">phone</span>
                    <div>
                        <span class="font-brand text-[#526600] text-xs font-bold"><?= htmlspecialchars($order['phone']) ?></span>
                        <?php if (!empty($order['phone2'])): ?>
                            <span class="ml-2 text-[#49473f] text-xs">/ <?= htmlspecialchars($order['phone2']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#49473f] shrink-0" style="font-size:16px">location_on</span>
                    <span class="text-[#1b1c1c] leading-snug"><?= htmlspecialchars($order['address']) ?></span>
                </div>
                <?php if (!empty($order['note'])): ?>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#49473f] shrink-0" style="font-size:16px">note</span>
                    <span class="text-[#49473f] italic leading-snug"><?= htmlspecialchars($order['note']) ?></span>
                </div>
                <?php endif; ?>
                <div class="flex items-center gap-3 border-t border-[#eae8e7] pt-3">
                    <span class="material-symbols-outlined text-[#49473f]" style="font-size:16px">schedule</span>
                    <span class="text-[#49473f] text-xs"><?= $createdAt ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>

</main>

<!-- ─── FOOTER ─── -->
<footer class="border-t-4 border-[#1b1c1c] bg-[#eae8e7] mt-12">
    <div class="max-w-[1400px] mx-auto px-4 md:px-12 py-8 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
        <div>
            <div class="font-brand text-lg font-bold italic text-[#bb0509]">PIXEL_VAULT</div>
            <div class="font-brand text-[10px] text-[#49473f] mt-1 uppercase tracking-widest">ADMIN PANEL</div>
        </div>
        <div class="text-center">
            <div class="flex items-center justify-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="font-brand text-[10px] text-[#49473f] uppercase tracking-widest">HỆ THỐNG ĐANG HOẠT ĐỘNG</span>
            </div>
            <div class="font-brand text-[9px] text-[#49473f]">ĐĂNG NHẬP: QUẢN_TRỊ_VIÊN</div>
        </div>
        <div class="font-brand text-[10px] text-[#49473f] md:text-right">© 1989–2026 PIXEL VAULT</div>
    </div>
</footer>

</body>
</html>

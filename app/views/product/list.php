<?php 
$products = $products ?? [];
$filters = $filters ?? ['q' => '', 'category' => '', 'condition' => '', 'resolution' => '', 'rom_format' => '', 'players' => '', 'genre' => ''];
$systemOptions = $systemOptions ?? [];
$totalProducts = $totalProducts ?? count($products);
$totalFilteredProducts = $totalFilteredProducts ?? count($products);
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$perPage = $perPage ?? 4;
$cartQuantity = array_sum(array_map(fn($item) => (int) ($item['quantity'] ?? 0), $_SESSION['cart'] ?? []));
$pageUrl = function (int $page) use ($filters): string {
    $params = array_filter($filters, fn($value) => $value !== '');
    $params['page'] = $page;

    return url('Product/list') . '?' . http_build_query($params);
};
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIXEL VAULT – Cửa Hàng Game Retro</title>
    <link rel="icon" type="image/png" href="<?= url('uploads/pixel-vault-icon.png') ?>">
    <link rel="apple-touch-icon" href="<?= url('uploads/pixel-vault-icon.png') ?>">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700&family=Space+Mono:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Be Vietnam Pro', sans-serif; line-height: 1.5; }
        .font-brand { font-family: 'Space Mono', monospace; }
        h1, h2, h3, .font-brand { line-height: 1.25; }
        button, a, .btn-press, .nav-link { line-height: 1.35; }
        :root {
            --ease-out-soft: cubic-bezier(.16, 1, .3, 1);
            --ease-snap: cubic-bezier(.2, .8, .2, 1);
        }

        .dot-matrix {
            background-image: radial-gradient(#cac6bc 1px, transparent 1px);
            background-size: 8px 8px;
        }
        .scanlines {
            background: linear-gradient(to bottom, rgba(255,255,255,0) 50%, rgba(0,0,0,0.06) 50%);
            background-size: 100% 4px;
            pointer-events: none;
        }

        .shadow-brutal    { box-shadow: 8px 8px 0px 0px #1b1c1c; }
        .shadow-brutal-sm { box-shadow: 4px 4px 0px 0px #1b1c1c; }
        .shadow-brutal-lg { box-shadow: 12px 12px 0px 0px #1b1c1c; }
        .shadow-brutal-xs { box-shadow: 2px 2px 0px 0px #1b1c1c; }

        .game-card {
            transition: transform .26s var(--ease-out-soft), box-shadow .26s var(--ease-out-soft), border-color .26s ease;
            will-change: transform;
        }
        .game-card:hover {
            transform: translate(-3px, -5px);
            box-shadow: 13px 13px 0px 0px #1b1c1c !important;
        }
        .crt-panel {
            background-color: #b2d42f;
            background-image:
                linear-gradient(to bottom, rgba(255,255,255,0) 50%, rgba(0,0,0,.08) 50%),
                radial-gradient(rgba(82,102,0,.24) 1px, transparent 1px);
            background-size: 100% 4px, 8px 8px;
        }
        .pixel-placeholder {
            width: calc(100% - 2rem);
            height: calc(100% - 2rem);
            border: 2px dashed #526600;
            background: rgba(251,249,248,.34);
        }
        .product-fit-image {
            object-fit: contain;
            background: rgba(251,249,248,.18);
        }
        .product-card-media {
            aspect-ratio: 1 / 1;
            min-height: 0;
            max-height: 430px;
        }
        .product-image-stage {
            width: calc(100% - 1rem);
            height: calc(100% - 1rem);
            background: #f5f0e6;
            border: 2px solid #526600;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: inset 0 0 0 8px rgba(219,255,92,.16);
            transition: transform .28s var(--ease-out-soft), box-shadow .28s var(--ease-out-soft);
        }
        .product-card-image {
            object-fit: contain;
            object-position: center;
            background: #f5f0e6;
            width: 94%;
            height: 94%;
            transition: transform .32s var(--ease-out-soft), filter .32s ease;
        }
        .game-card:hover .product-image-stage {
            transform: translateY(-2px);
            box-shadow: inset 0 0 0 8px rgba(219,255,92,.2), 0 8px 18px rgba(27,28,28,.12);
        }
        .game-card:hover .product-card-image {
            transform: scale(1.025);
            filter: saturate(1.04) contrast(1.02);
        }
        .hero-console-frame {
            width: min(360px, 82vw);
            aspect-ratio: 1 / 1;
            overflow: hidden;
        }
        .hero-console-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .hero-product-photo {
            transform: scale(1.18);
            transform-origin: center;
            transition: transform .55s var(--ease-out-soft), filter .45s ease;
        }
        .hero-console-frame:hover .hero-product-photo {
            transform: scale(1.21) rotate(-.35deg);
            filter: saturate(1.04) contrast(1.02);
        }
        .icon-buy {
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
            box-shadow: 4px 4px 0 #1b1c1c;
            transition: transform .18s var(--ease-snap), box-shadow .18s var(--ease-snap), filter .18s ease;
        }
        .icon-buy:hover {
            transform: translate(-2px, -3px) rotate(-3deg);
            box-shadow: 7px 7px 0 #1b1c1c;
            filter: brightness(1.05);
        }
        .icon-buy:active {
            transform: translate(4px, 4px);
            box-shadow: none;
        }
        input.filter-radio {
            width: 1.05rem;
            height: 1.05rem;
            appearance: none !important;
            -webkit-appearance: none !important;
            border: 2px solid #1b1c1c;
            background: #fbf9f8 !important;
            background-image: none !important;
            margin: 0;
            display: inline-grid;
            place-content: center;
            flex: 0 0 auto;
            color: #bb0509 !important;
            box-shadow: none !important;
        }
        input.filter-radio::before {
            content: "";
            width: .45rem;
            height: .45rem;
            transform: scale(0);
            background: #1b1c1c;
            transition: transform .1s ease;
        }
        input.filter-radio:checked {
            background: #bb0509 !important;
            background-image: none !important;
        }
        input.filter-radio:checked::before {
            transform: scale(1);
        }
        input.filter-radio-round {
            border-radius: 9999px;
        }
        input.filter-radio-round::before {
            border-radius: 9999px;
        }
        .filter-accordion > summary {
            list-style: none;
        }
        .filter-accordion > summary::-webkit-details-marker {
            display: none;
        }
        .filter-accordion .summary-icon {
            transition: transform .2s var(--ease-out-soft);
        }
        .filter-accordion[open] .summary-icon {
            transform: rotate(180deg);
        }
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height .28s var(--ease-out-soft), border-color .2s ease;
            border-top: 0 solid #1b1c1c;
        }
        .mobile-menu.open {
            max-height: 320px;
            border-top-width: 4px;
        }
        .mobile-filter-toggle {
            display: none;
        }
        .filter-accordion {
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s var(--ease-snap);
        }
        .filter-accordion:hover {
            border-color: #cac6bc;
            box-shadow: 3px 3px 0 rgba(27,28,28,.16);
            transform: translate(-1px, -1px);
        }

        .btn-press { transition: transform .18s var(--ease-snap), box-shadow .18s var(--ease-snap), filter .18s ease; }
        .btn-press:hover  { transform: translate(-2px,-3px); box-shadow: 8px 8px 0 #1b1c1c; filter: brightness(1.03); }
        .btn-press:active { transform: translate(2px, 2px);  box-shadow: 2px 2px 0 #1b1c1c; }

        .nav-link { transition: transform .18s var(--ease-snap), color .18s ease, opacity .18s ease; }
        .nav-link:hover  { transform: translate(1px,-1px); }
        .nav-link:active { transform: translate(1px, 1px); }
        .motion-reveal {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity .55s var(--ease-out-soft), transform .55s var(--ease-out-soft);
        }
        .motion-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .motion-reveal[data-reveal-delay="1"] { transition-delay: .06s; }
        .motion-reveal[data-reveal-delay="2"] { transition-delay: .12s; }
        .motion-reveal[data-reveal-delay="3"] { transition-delay: .18s; }
        .results-transition {
            transition: opacity .22s ease, transform .22s var(--ease-out-soft);
        }
        .results-transition.is-loading {
            opacity: .46;
            transform: translateY(6px);
        }
        input:focus-visible, select:focus-visible, textarea:focus-visible, button:focus-visible, a:focus-visible {
            outline: 3px solid #dbff5c;
            outline-offset: 3px;
        }

        /* ── HERO FIX: tăng line-height, tăng padding top/bottom để dấu TV không bị dính ── */
        .hero-title {
            line-height: 1.28 !important;
            padding-top: 1.2rem !important;
            padding-bottom: 1.2rem !important;
        }

        /* ── TICKER ── */
        @keyframes ticker { 0%{transform:translate3d(0,0,0)} 100%{transform:translate3d(-50%,0,0)} }
        .ticker-viewport {
            overflow: hidden;
        }
        .ticker-track {
            display: flex;
            width: max-content;
            animation: ticker 24s linear infinite;
            white-space: nowrap;
            will-change: transform;
        }
        .ticker-item {
            flex: 0 0 auto;
            padding-right: 2rem;
        }
        .ticker-track:hover { animation-play-state: paused; }

        /* ── MODAL ── */
        .overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.55); z-index: 200;
            backdrop-filter: blur(2px);
        }
        .overlay.active { display: flex; align-items: flex-start; justify-content: center; padding-top: 80px; animation: overlayFade .18s ease; }
        .modal-box {
            background: #fbf9f8; border: 4px solid #1b1c1c;
            box-shadow: 12px 12px 0 #1b1c1c;
            max-width: 520px; width: 92%; max-height: 80vh; overflow-y: auto;
            animation: popIn .24s var(--ease-out-soft);
        }
        @keyframes overlayFade {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes popIn {
            from { transform: translate(4px,16px) scale(.965); opacity:0; }
            to   { transform: translate(0,0) scale(1); opacity:1; }
        }

        /* ── CART PANEL ── */
        .cart-panel {
            position: fixed; top: 0; right: 0; bottom: 0;
            width: min(400px, 92vw);
            background: #fbf9f8;
            border-left: 4px solid #1b1c1c;
            box-shadow: -8px 0 0 #1b1c1c;
            z-index: 300;
            transform: translateX(110%);
            transition: transform .32s var(--ease-out-soft);
            display: flex; flex-direction: column;
        }
        .cart-panel.open { transform: translateX(0); }
        .cart-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.45); z-index: 299;
        }
        .cart-overlay.open { display: block; }
        @media (max-width: 767px) {
            body {
                overflow-x: hidden;
            }
            .ticker-track {
                animation-duration: 18s;
            }
            .ticker-item {
                padding-right: 1rem;
            }
            .hero-title {
                font-size: 1.9rem !important;
                padding: 1rem !important;
                line-height: 1.22 !important;
                width: 100%;
            }
            .hero-console-frame {
                width: min(320px, 88vw);
                margin-top: .5rem;
            }
            .hero-product-photo {
                transform: scale(1.12);
            }
            .hero-console-frame:hover .hero-product-photo {
                transform: scale(1.14);
            }
            #games {
                scroll-margin-top: 104px;
            }
            .mobile-filter-toggle {
                display: flex;
            }
            #product-filter-form {
                display: none;
            }
            #product-filter-shell.open #product-filter-form {
                display: block;
            }
            .product-card-media {
                max-height: none;
            }
            .game-card:hover {
                transform: none;
                box-shadow: 8px 8px 0px 0px #1b1c1c !important;
            }
            .product-card-image {
                width: 92%;
                height: 92%;
            }
            .icon-buy:hover {
                transform: none;
                box-shadow: 4px 4px 0 #1b1c1c;
            }
            .mobile-icon-button {
                width: 2.75rem;
                height: 2.75rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 2px solid #1b1c1c;
                background: #fff;
                color: #615e57;
            }
            .cart-panel {
                top: auto;
                left: 0;
                right: 0;
                bottom: 0;
                width: 100%;
                max-height: 64vh;
                border-left: 0;
                border-top: 4px solid #1b1c1c;
                box-shadow: 0 -8px 0 #1b1c1c;
                transform: translateY(110%);
                border-radius: 0;
            }
            .cart-panel.open {
                transform: translateY(0);
            }
            .cart-panel .cart-header {
                padding: .75rem 1rem;
            }
            .cart-panel #cart-items {
                padding: 1rem;
                min-height: 120px;
            }
            .cart-panel .cart-footer {
                padding: 1rem;
            }
            .cart-panel .cart-empty-state {
                padding-top: 1.75rem;
                padding-bottom: 1.75rem;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
            .motion-reveal {
                opacity: 1;
                transform: none;
            }
            .ticker-track {
                animation: ticker 36s linear infinite !important;
            }
        }
    </style>
</head>
<body class="bg-[#fbf9f8] text-[#1b1c1c] dot-matrix min-h-screen">

<!-- ─── CART SLIDE PANEL ─── -->
<div class="cart-overlay" id="cart-overlay" onclick="closeCart()"></div>
<aside class="cart-panel" id="cart-panel">
    <div class="cart-header bg-[#1b1c1c] px-6 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-[#dbff5c]" style="font-variation-settings:'FILL' 1;font-size:20px">shopping_cart</span>
            <span class="font-brand text-white text-[11px] font-bold uppercase tracking-[.12em]">GIỎ HÀNG</span>
        </div>
        <button onclick="closeCart()" class="text-zinc-400 hover:text-white transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div id="cart-items" class="flex-1 overflow-y-auto p-6 space-y-3">
        <div id="cart-empty" class="cart-empty-state text-center py-16">
            <span class="material-symbols-outlined text-[#cac6bc] block mb-4" style="font-size:56px">sports_esports</span>
            <p class="font-bold text-[#1b1c1c] uppercase text-sm tracking-widest mb-1">Giỏ hàng trống!</p>
            <p class="text-[#49473f] text-sm">Thêm game vào giỏ để bắt đầu</p>
        </div>
    </div>
    <div class="cart-footer border-t-4 border-[#1b1c1c] p-6 space-y-3 shrink-0 bg-[#f0eeec]">
        <div class="flex justify-between items-center">
            <span class="text-[11px] font-bold uppercase tracking-[.12em] text-[#49473f]">Tổng cộng</span>
            <span id="cart-total" class="font-brand text-xl font-bold text-[#bb0509]">0đ</span>
        </div>
        <div id="cart-message" class="hidden border-2 border-[#526600] bg-[#dbff5c] px-3 py-2 text-[11px] font-bold text-[#3d4c00]"></div>
        <button class="btn-press w-full bg-[#bb0509] text-white py-4 border-4 border-[#1b1c1c] shadow-brutal text-[11px] font-bold uppercase tracking-[.12em] flex items-center justify-center gap-2"
                onclick="checkoutCart()">
            <span class="material-symbols-outlined" style="font-size:18px">payment</span>
            THANH TOÁN NGAY
        </button>
        <button onclick="clearCart()" class="w-full py-3 border-2 border-[#cac6bc] text-[10px] font-bold uppercase tracking-widest text-[#49473f] hover:border-[#bb0509] hover:text-[#bb0509] transition-all">
            XÓA GIỎ HÀNG
        </button>
    </div>
</aside>

<!-- ─── MODALS ─── -->

<!-- Bảng Điểm -->
<div class="overlay" id="modal-leaderboard" onclick="closeModalOutside(event,'modal-leaderboard')">
    <div class="modal-box">
        <div class="bg-[#1b1c1c] px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#dbff5c]" style="font-variation-settings:'FILL' 1">leaderboard</span>
                <span class="font-brand text-white text-[11px] font-bold uppercase tracking-[.12em]">BẢNG ĐIỂM CAO NHẤT</span>
            </div>
            <button onclick="closeModal('modal-leaderboard')" class="text-zinc-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-3">
            <p class="text-[11px] text-[#49473f] uppercase tracking-widest font-bold mb-4">Top Người Sưu Tập · Mùa 1989</p>
            <?php
            $lb = [
                ['rank'=>1,'name'=>'RetroKing_HCM', 'score'=>'12.450','medal'=>'bg-[#dbff5c] text-[#3d4c00]'],
                ['rank'=>2,'name'=>'PixelHunter_HN','score'=>'9.800', 'medal'=>'bg-zinc-300 text-zinc-700'],
                ['rank'=>3,'name'=>'8BitCollector',  'score'=>'8.100', 'medal'=>'bg-[#bb0509] text-white'],
                ['rank'=>4,'name'=>'VaultMaster99',  'score'=>'6.500', 'medal'=>'bg-zinc-800 text-zinc-400'],
                ['rank'=>5,'name'=>'GameTape_DaN',   'score'=>'5.200', 'medal'=>'bg-zinc-800 text-zinc-400'],
            ];
            foreach ($lb as $r): ?>
            <div class="flex items-center gap-4 p-3 border-2 border-[#e4e2e1] hover:border-[#1b1c1c] transition-colors">
                <span class="font-brand text-[10px] font-bold w-7 h-7 flex items-center justify-center border-2 border-[#1b1c1c] <?= $r['medal'] ?>">#<?= $r['rank'] ?></span>
                <span class="flex-1 font-semibold text-sm"><?= $r['name'] ?></span>
                <span class="font-brand text-[#526600] font-bold text-sm"><?= $r['score'] ?> pts</span>
            </div>
            <?php endforeach; ?>
            <div class="mt-4 bg-[#dbff5c] border-4 border-[#1b1c1c] p-4 shadow-brutal-sm">
                <p class="text-[11px] font-bold text-[#3d4c00]">💡 Mỗi lần mua hàng = +100 pts &nbsp;·&nbsp; Đánh giá sản phẩm = +20 pts</p>
            </div>
        </div>
    </div>
</div>

<!-- Giới Thiệu -->
<div class="overlay" id="modal-about" onclick="closeModalOutside(event,'modal-about')">
    <div class="modal-box">
        <div class="bg-[#1b1c1c] px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#dbff5c]" style="font-variation-settings:'FILL' 1">info</span>
                <span class="font-brand text-white text-[11px] font-bold uppercase tracking-[.12em]">VỀ PIXEL VAULT</span>
            </div>
            <button onclick="closeModal('modal-about')" class="text-zinc-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-5">
            <div class="inline-block bg-[#dbff5c] border-2 border-[#1b1c1c] shadow-brutal-sm px-3 py-1.5 text-[9px] font-bold uppercase tracking-widest text-[#3d4c00] -rotate-1">EST. 1989</div>
            <h2 class="text-2xl font-extrabold uppercase">Câu Chuyện Của Chúng Tôi</h2>
            <p class="text-sm text-[#49473f] leading-relaxed"><strong>Pixel Vault</strong> ra đời từ niềm đam mê với những tựa game cổ điển thời 8-bit. Chúng tôi tin rằng mỗi băng game cũ đều ẩn chứa một ký ức tuổi thơ đáng được gìn giữ.</p>
            <p class="text-sm text-[#49473f] leading-relaxed">Kho hàng được tuyển chọn kỹ lưỡng — mỗi sản phẩm qua kiểm tra chất lượng trước khi đến tay bạn.</p>
            <div class="grid grid-cols-3 gap-3">
                <?php foreach ([['sports_esports','500+','Tựa Game'],['group','2K+','Khách Hàng'],['star','4.9','Đánh Giá']] as $s): ?>
                <div class="text-center p-4 border-4 border-[#1b1c1c] bg-white shadow-brutal-sm">
                    <span class="material-symbols-outlined text-[#bb0509] block mb-1" style="font-variation-settings:'FILL' 1;font-size:28px"><?= $s[0] ?></span>
                    <p class="font-brand font-bold text-lg"><?= $s[1] ?></p>
                    <p class="text-[9px] uppercase tracking-widest text-[#49473f] font-bold"><?= $s[2] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chính Sách -->
<div class="overlay" id="modal-policy" onclick="closeModalOutside(event,'modal-policy')">
    <div class="modal-box">
        <div class="bg-[#1b1c1c] px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#dbff5c]" style="font-variation-settings:'FILL' 1">policy</span>
                <span class="font-brand text-white text-[11px] font-bold uppercase tracking-[.12em]">CHÍNH SÁCH BÁN HÀNG</span>
            </div>
            <button onclick="closeModal('modal-policy')" class="text-zinc-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-3">
            <?php foreach ([
                ['verified','Hàng Chính Hãng','Tất cả sản phẩm được kiểm tra kỹ lưỡng, đảm bảo hoạt động tốt trước khi giao hàng.'],
                ['local_shipping','Giao Hàng Toàn Quốc','Đóng gói cẩn thận, giao qua GHTK / GHN trong 2–5 ngày làm việc.'],
                ['replay','Đổi Trả 7 Ngày','Sản phẩm lỗi do vận chuyển hoặc không đúng mô tả sẽ được đổi/hoàn tiền trong 7 ngày.'],
                ['support_agent','Hỗ Trợ 24/7','Liên hệ qua Zalo hoặc email — đội ngũ chúng tôi luôn sẵn sàng.'],
            ] as [$icon,$title,$body]): ?>
            <div class="flex gap-3 p-3 border-2 border-[#e4e2e1] hover:border-[#1b1c1c] transition-colors">
                <span class="material-symbols-outlined text-[#bb0509] shrink-0" style="font-variation-settings:'FILL' 1"><?= $icon ?></span>
                <div>
                    <p class="font-bold text-[11px] uppercase tracking-widest mb-1"><?= $title ?></p>
                    <p class="text-sm text-[#49473f]"><?= $body ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Điều Khoản -->
<div class="overlay" id="modal-terms" onclick="closeModalOutside(event,'modal-terms')">
    <div class="modal-box">
        <div class="bg-[#1b1c1c] px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#dbff5c]" style="font-variation-settings:'FILL' 1">gavel</span>
                <span class="font-brand text-white text-[11px] font-bold uppercase tracking-[.12em]">ĐIỀU KHOẢN SỬ DỤNG</span>
            </div>
            <button onclick="closeModal('modal-terms')" class="text-zinc-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 text-sm text-[#49473f] leading-relaxed space-y-3">
            <p class="font-bold text-[#1b1c1c] uppercase text-[11px] tracking-widest">1. Điều Kiện Mua Hàng</p>
            <p>Người mua phải từ 16 tuổi trở lên. Đặt hàng đồng nghĩa với việc chấp nhận toàn bộ điều khoản này.</p>
            <p class="font-bold text-[#1b1c1c] uppercase text-[11px] tracking-widest mt-3">2. Thanh Toán</p>
            <p>Chúng tôi chấp nhận chuyển khoản ngân hàng, COD và ví điện tử MoMo/ZaloPay.</p>
            <p class="font-bold text-[#1b1c1c] uppercase text-[11px] tracking-widest mt-3">3. Bảo Mật Thông Tin</p>
            <p>Thông tin cá nhân của bạn được bảo vệ tuyệt đối, không chia sẻ cho bên thứ ba.</p>
            <p class="font-bold text-[#1b1c1c] uppercase text-[11px] tracking-widest mt-3">4. Giải Quyết Tranh Chấp</p>
            <p>Mọi tranh chấp sẽ được giải quyết qua thương lượng. Pixel Vault cam kết đặt lợi ích khách hàng lên hàng đầu.</p>
        </div>
    </div>
</div>

<!-- Hỗ Trợ -->
<div class="overlay" id="modal-support" onclick="closeModalOutside(event,'modal-support')">
    <div class="modal-box">
        <div class="bg-[#1b1c1c] px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#dbff5c]" style="font-variation-settings:'FILL' 1">headset_mic</span>
                <span class="font-brand text-white text-[11px] font-bold uppercase tracking-[.12em]">LIÊN HỆ HỖ TRỢ</span>
            </div>
            <button onclick="closeModal('modal-support')" class="text-zinc-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-3">
            <?php foreach ([
                ['smartphone','Zalo / Phone','0901 234 567','text-[#bb0509]'],
                ['mail','Email','hello@pixelvault.vn','text-[#526600]'],
                ['schedule','Giờ Làm Việc','T2–T7 · 9:00 – 21:00','text-[#1b1c1c]'],
                ['location_on','Địa Chỉ','TP. Hồ Chí Minh, Việt Nam','text-[#1b1c1c]'],
            ] as [$icon,$label,$val,$color]): ?>
            <div class="flex items-center gap-4 p-4 border-2 border-[#e4e2e1] hover:border-[#1b1c1c] transition-colors">
                <span class="material-symbols-outlined <?= $color ?> shrink-0" style="font-variation-settings:'FILL' 1"><?= $icon ?></span>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-[#49473f]"><?= $label ?></p>
                    <p class="font-semibold text-[#1b1c1c]"><?= $val ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="bg-[#dbff5c] border-4 border-[#1b1c1c] p-4 shadow-brutal-sm text-[12px] text-[#3d4c00] font-medium mt-4">
                🎮 Phản hồi trong vòng <strong>30 phút</strong> trong giờ làm việc.
            </div>
        </div>
    </div>
</div>

<?php $activePage = 'home'; include __DIR__ . '/../shares/siteHeader.php'; ?>

<main class="max-w-[1200px] mx-auto px-4 md:px-12 py-8 md:py-12 space-y-16 md:space-y-24">

    <!-- ─── HERO ─── -->
    <section class="flex flex-col md:flex-row items-center gap-8 md:gap-12 pt-6 md:pt-8">
        <div class="flex-1 space-y-6 z-10">
            <div class="inline-block bg-[#dbff5c] text-[#3d4c00] px-4 py-2 border-2 border-[#1b1c1c] shadow-brutal-sm -rotate-2 text-[11px] font-bold uppercase tracking-[.12em]">
                LEVEL 1: BẮT ĐẦU
            </div>

            <!-- FIX applied via hero-title class -->
            <h1 class="hero-title text-3xl md:text-5xl font-extrabold uppercase bg-[#fbf9f8] inline-block border-4 border-[#1b1c1c] px-4 shadow-brutal-lg">
                Ký ức tuổi thơ<br>trở lại
            </h1>

            <p class="text-base md:text-lg text-[#49473f] bg-[#fbf9f8] p-4 border-2 border-[#cac6bc] shadow-brutal-sm max-w-lg leading-relaxed">
                Khám phá bộ sưu tập băng game cổ điển, máy chơi game retro 8-bit và phụ kiện vintage được tuyển chọn kỹ lưỡng. Đã đến lúc chơi game như hồi 1989!
            </p>

            <a href="#games" class="inline-block bg-[#bb0509] text-white px-8 py-4 border-4 border-[#1b1c1c] shadow-brutal font-bold uppercase tracking-[.12em] text-sm btn-press">
                KHÁM PHÁ NGAY
            </a>
        </div>

        <div class="flex-1 flex items-center justify-center relative min-h-[300px]">
            <div class="hero-console-frame relative bg-[#fbf9f8] border-4 border-[#1b1c1c] shadow-brutal-lg p-4 rotate-2">
                <div class="absolute inset-0 scanlines z-20"></div>
                <div class="absolute inset-3 border-2 border-[#cac6bc] pointer-events-none z-10"></div>
                <img src="<?= url('uploads/hero-retro-handheld.png') ?>"
                     alt="Máy chơi game handheld retro dùng băng Pixel Vault"
                     class="hero-product-photo relative z-10">
                <div class="absolute top-5 left-5 bg-[#ba1a1a] text-white text-[9px] font-bold uppercase tracking-widest px-2 py-1 border-2 border-[#1b1c1c] shadow-[2px_2px_0_#1b1c1c] z-30 -rotate-12">
                    RETRO
                </div>
            </div>
        </div>
    </section>

    <!-- ─── PRODUCT GRID ─── -->
    <section id="games" class="space-y-10">
        <div class="flex flex-wrap items-center gap-4">
            <div class="h-1 flex-1 bg-[#1b1c1c]"></div>
            <h2 class="text-xl md:text-2xl font-extrabold uppercase px-4 py-2 bg-[#fbf9f8] border-4 border-[#1b1c1c] shadow-brutal-sm">CỬA HÀNG RETRO</h2>
            <a href="<?= url('Product/add') ?>"
               class="btn-press bg-[#bb0509] text-white px-5 py-3 border-4 border-[#1b1c1c] shadow-brutal-sm text-[11px] font-bold uppercase tracking-[.12em]">
                + THÊM GAME
            </a>
            <div class="h-1 flex-1 bg-[#1b1c1c]"></div>
        </div>

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 border-b-4 border-[#1b1c1c] pb-4">
            <p class="text-sm text-[#49473f] font-medium">Băng game cổ điển · 1989 – 2026</p>
            <span id="product-count" class="self-start md:self-auto bg-[#e4e2e1] border-2 border-[#1b1c1c] shadow-brutal-xs px-3 py-1 font-brand text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f]">
                Hiển thị: <?= count($products) ?>/<?= $totalFilteredProducts ?> tựa · Trang <?= $currentPage ?>/<?= $totalPages ?>
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-8 items-start">
            <aside id="product-filter-shell" class="lg:sticky lg:top-32">
                <button type="button" onclick="toggleMobileFilters()" class="mobile-filter-toggle w-full items-center justify-between bg-[#fbf9f8] border-4 border-[#1b1c1c] shadow-brutal-sm px-4 py-3 mb-4">
                    <span class="font-brand text-sm font-bold uppercase tracking-[.12em]">Load Data</span>
                    <span class="material-symbols-outlined" style="font-size:22px">tune</span>
                </button>
                <form id="product-filter-form" method="GET" action="<?= url('Product/list') ?>" class="bg-[#fbf9f8] border-4 border-[#1b1c1c] shadow-brutal p-4 space-y-4">
                    <div class="border-b-4 border-[#1b1c1c] pb-2.5">
                        <h3 class="font-brand text-lg font-bold uppercase tracking-[.08em]">Load Data</h3>
                        <p class="mt-1 text-[11px] text-[#49473f] leading-relaxed">Tìm và lọc kho game theo thông tin hệ thống.</p>
                    </div>

                    <label class="block">
                        <span class="mb-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f]">
                            <span class="material-symbols-outlined text-[#bb0509]" style="font-size:16px">search</span>
                            Tìm kiếm
                        </span>
                        <input name="q" value="<?= htmlspecialchars($filters['q']) ?>" placeholder="Tên game, khu vực..."
                               class="w-full border-2 border-[#1b1c1c] bg-white px-3 py-2 text-sm font-semibold focus:border-[#bb0509] focus:ring-0">
                    </label>

                    <?php
                    $filterGroups = [
                        'category' => 'Loại băng',
                        'condition' => 'Tình trạng',
                        'resolution' => 'Độ phân giải',
                        'rom_format' => 'Định dạng',
                        'players' => 'Số người chơi',
                    ];
                    foreach ($filterGroups as $field => $label):
                        $isOpen = $filters[$field] !== '';
                    ?>
                    <details class="filter-accordion border-2 border-[#e4e2e1] bg-white" data-filter-field="<?= $field ?>" <?= $isOpen ? 'open' : '' ?>>
                        <summary class="flex cursor-pointer items-center justify-between gap-3 px-3 py-2.5 hover:bg-[#f5f0e6]">
                            <span>
                                <span class="block font-brand text-[10px] font-bold uppercase tracking-[.16em] text-[#49473f]"><?= $label ?></span>
                                <?php if ($filters[$field] !== ''): ?>
                                <span class="filter-current mt-1 block truncate text-[11px] font-bold text-[#bb0509]"><?= htmlspecialchars($filters[$field]) ?></span>
                                <?php endif; ?>
                            </span>
                            <span class="summary-icon material-symbols-outlined text-[#49473f]" style="font-size:20px">expand_more</span>
                        </summary>
                        <div class="space-y-3 border-t-2 border-dashed border-[#cac6bc] px-3 py-3">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="<?= $field ?>" value="" <?= $filters[$field] === '' ? 'checked' : '' ?> class="filter-radio filter-radio-round focus:ring-0 focus:ring-offset-0">
                                <span class="text-sm font-medium group-hover:text-[#bb0509] transition-colors">Tất cả</span>
                            </label>
                            <?php foreach (($systemOptions[$field] ?? []) as $option): ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="<?= $field ?>" value="<?= htmlspecialchars($option) ?>" <?= $filters[$field] === $option ? 'checked' : '' ?> class="filter-radio <?= $field === 'resolution' ? '' : 'filter-radio-round' ?> focus:ring-0 focus:ring-offset-0">
                                <span class="text-sm font-medium group-hover:text-[#bb0509] transition-colors"><?= htmlspecialchars($option) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </details>
                    <?php endforeach; ?>

                    <?php $isGenreOpen = ($filters['genre'] ?? '') !== ''; ?>
                    <details class="filter-accordion border-2 border-[#e4e2e1] bg-white" data-filter-field="genre" <?= $isGenreOpen ? 'open' : '' ?>>
                        <summary class="flex cursor-pointer items-center justify-between gap-3 px-3 py-2.5 hover:bg-[#f5f0e6]">
                            <span>
                                <span class="block font-brand text-[10px] font-bold uppercase tracking-[.16em] text-[#49473f]">Thể loại</span>
                                <?php if (($filters['genre'] ?? '') !== ''): ?>
                                <span class="filter-current mt-1 block truncate text-[11px] font-bold text-[#bb0509]"><?= htmlspecialchars($filters['genre']) ?></span>
                                <?php endif; ?>
                            </span>
                            <span class="summary-icon material-symbols-outlined text-[#49473f]" style="font-size:20px">expand_more</span>
                        </summary>
                        <div class="space-y-3 border-t-2 border-dashed border-[#cac6bc] px-3 py-3">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="genre" value="" <?= ($filters['genre'] ?? '') === '' ? 'checked' : '' ?> class="filter-radio filter-radio-round focus:ring-0 focus:ring-offset-0">
                                <span class="text-sm font-medium group-hover:text-[#bb0509] transition-colors">Tất cả</span>
                            </label>
                            <?php foreach (($systemOptions['genres'] ?? []) as $option): ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="genre" value="<?= htmlspecialchars($option) ?>" <?= ($filters['genre'] ?? '') === $option ? 'checked' : '' ?> class="filter-radio filter-radio-round focus:ring-0 focus:ring-offset-0">
                                <span class="text-sm font-medium group-hover:text-[#bb0509] transition-colors"><?= htmlspecialchars($option) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </details>

                    <div class="space-y-3 pt-2">
                        <button class="btn-press w-full bg-[#bb0509] text-white py-2.5 border-4 border-[#1b1c1c] shadow-brutal-sm font-brand text-sm font-bold uppercase tracking-[.08em]">
                            Apply Hacks
                        </button>
                        <a id="product-filter-reset" href="<?= url('Product/list') ?>" class="block w-full text-center border-2 border-[#cac6bc] px-4 py-2.5 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f] hover:border-[#1b1c1c] hover:bg-white transition-all">
                            Reset Data
                        </a>
                    </div>
                </form>
            </aside>

            <div class="min-w-0">
                <div id="active-filters" class="mb-5 flex flex-wrap gap-2 <?= empty(array_filter($filters)) ? 'hidden' : '' ?>">
                    <?php foreach (array_filter($filters) as $active): ?>
                    <span class="bg-[#dbff5c] border-2 border-[#1b1c1c] px-3 py-1 font-brand text-[10px] font-bold text-[#3d4c00] shadow-brutal-xs">
                        <?= htmlspecialchars($active) ?>
                    </span>
                    <?php endforeach; ?>
                </div>

                <div id="product-results" class="results-transition">
                <?php if (empty($products)): ?>
                <div class="text-center py-24 border-4 border-[#1b1c1c] bg-[#fbf9f8] shadow-brutal">
                    <div class="font-brand text-7xl text-[#e4e2e1] mb-6 select-none">404</div>
                    <p class="text-2xl font-extrabold text-[#bb0509] uppercase mb-3"><?= $totalProducts > 0 ? 'Không tìm thấy game!' : 'Kho Game Trống!' ?></p>
                    <p class="text-[#49473f] mb-8"><?= $totalProducts > 0 ? 'Thử đổi từ khóa hoặc nới bộ lọc để thấy thêm sản phẩm.' : 'Chưa có game nào. Hãy thêm tựa game đầu tiên vào kho nhé.' ?></p>
                    <a href="<?= url('Product/add') ?>"
                       class="inline-block btn-press bg-[#1b1c1c] text-white px-10 py-5 border-4 border-[#bb0509] font-bold uppercase tracking-[.12em] shadow-[4px_4px_0_#bb0509]">
                        THÊM GAME ĐẦU TIÊN
                    </a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-7 gap-y-10">
                    <?php foreach ($products as $product): ?>
                    <?php $primaryImage = $product->getPrimaryImage(); ?>
                    <article class="game-card bg-[#f5f0e6] border-4 border-[#1b1c1c] shadow-brutal overflow-visible flex flex-col relative group">
                        <div class="absolute -top-4 -right-4 bg-[#dbff5c] text-[#3d4c00] text-[9px] font-bold uppercase tracking-widest px-3 py-2 border-2 border-[#1b1c1c] shadow-brutal-xs rotate-12 z-20">CỔ ĐIỂN</div>

                        <div class="product-card-media crt-panel border-b-4 border-[#1b1c1c] relative overflow-hidden flex items-center justify-center p-2">
                            <?php if ($primaryImage): ?>
                            <div class="product-image-stage">
                                <img src="<?= htmlspecialchars($primaryImage) ?>" alt="<?= htmlspecialchars($product->getName()) ?>" class="product-fit-image product-card-image opacity-95 group-hover:opacity-100 transition-opacity">
                            </div>
                            <?php else: ?>
                            <div class="pixel-placeholder flex items-center justify-center">
                                <span class="font-brand text-6xl font-bold text-[#526600]/35 select-none">PX</span>
                            </div>
                            <?php endif; ?>
                            <div class="absolute bottom-3 left-3 bg-[#1b1c1c] text-white text-[9px] font-bold px-2 py-1 z-20 font-brand">
                                #<?= str_pad($product->getID(), 3, '0', STR_PAD_LEFT) ?>
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col bg-[#fbf9f8]">
                            <h3 class="font-brand text-lg font-bold uppercase leading-snug mb-2 line-clamp-2"><?= htmlspecialchars($product->getName()) ?></h3>
                                     <p class="text-sm text-[#49473f] leading-relaxed flex-1 mb-4"
                                         style="display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                                <?= htmlspecialchars($product->getDescription() ?: 'Tựa game retro kinh điển với đồ họa pixel và gameplay hấp dẫn không thể bỏ lỡ.') ?>
                            </p>

                            <?php if (!empty($product->getGenres())): ?>
                            <div class="mb-4 flex flex-wrap gap-1.5">
                                <?php foreach ($product->getGenres() as $genre): ?>
                                <span class="border-2 border-[#1b1c1c] bg-[#dbff5c] px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-[#3d4c00] shadow-brutal-xs">
                                    <?= htmlspecialchars($genre) ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <?php foreach ([
                                    ['category', $product->getCategory()],
                                    ['inventory_2', $product->getCondition()],
                                    ['aspect_ratio', $product->getResolution()],
                                    ['memory', $product->getRomFormat()],
                                    ['group', $product->getPlayers()],
                                ] as [$icon, $value]): ?>
                                <div class="flex items-center gap-1.5 border-2 border-[#e4e2e1] bg-white px-2 py-1.5 min-w-0 h-9">
                                    <span class="material-symbols-outlined text-[#526600] shrink-0" style="font-size:14px"><?= $icon ?></span>
                                    <span class="truncate text-[10px] font-bold uppercase tracking-[.04em] text-[#49473f]"><?= htmlspecialchars($value) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="border-t-2 border-dashed border-[#cac6bc] pt-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-brand text-2xl font-bold text-[#bb0509]"><?= number_format($product->getPrice(), 0, ',', '.') ?>đ</span>
                                    <a href="<?= url('Product/addToCart/' . $product->getID()) ?>"
                                       class="icon-buy bg-[#bb0509] text-white border-2 border-[#1b1c1c] flex items-center justify-center transition-all hover:-translate-y-0.5 hover:-translate-x-0.5"
                                       aria-label="Thêm vào giỏ hàng">
                                        <span class="material-symbols-outlined" style="font-size:22px">add_shopping_cart</span>
                                    </a>
                                </div>
                                <div class="flex gap-2">
                                    <a href="<?= url('Product/detail/' . $product->getID()) ?>"
                                       class="flex-1 text-center py-2 border-2 border-[#1b1c1c] text-[10px] font-bold uppercase tracking-widest hover:bg-[#1b1c1c] hover:text-white transition-all">CHI TIẾT</a>
                                    <a href="<?= url('Product/edit/' . $product->getID()) ?>"
                                       class="flex-1 text-center py-2 border-2 border-[#1b1c1c] text-[10px] font-bold uppercase tracking-widest hover:bg-[#dbff5c] transition-all">SỬA</a>
                                    <a href="<?= url('Product/delete/' . $product->getID()) ?>"
                                       onclick="return confirm('Xóa game «<?= htmlspecialchars($product->getName()) ?>» khỏi kho?')"
                                       class="flex-1 text-center py-2 border-2 border-[#bb0509] text-[#bb0509] text-[10px] font-bold uppercase tracking-widest hover:bg-[#bb0509] hover:text-white transition-all">XÓA</a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php if ($totalPages > 1): ?>
                <?php $paginationClick = "event.preventDefault(); loadFilteredProducts(this.href.replace(/#.*$/, '')).finally(() => document.getElementById('games')?.scrollIntoView({ behavior: 'smooth', block: 'start' }));"; ?>
                <nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="Phân trang sản phẩm">
                    <a href="<?= htmlspecialchars($pageUrl(max(1, $currentPage - 1))) ?>#games"
                       onclick="<?= htmlspecialchars($paginationClick, ENT_QUOTES, 'UTF-8') ?>"
                       class="pagination-link inline-flex h-10 min-w-10 items-center justify-center border-4 border-[#1b1c1c] bg-[#fbf9f8] px-3 font-brand text-[10px] font-bold uppercase tracking-[.12em] shadow-brutal-xs transition-all hover:bg-[#e4e2e1] <?= $currentPage <= 1 ? 'pointer-events-none opacity-40' : '' ?>">
                        ←
                    </a>
                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                    <a href="<?= htmlspecialchars($pageUrl($page)) ?>#games"
                       onclick="<?= htmlspecialchars($paginationClick, ENT_QUOTES, 'UTF-8') ?>"
                       class="pagination-link inline-flex h-10 min-w-10 items-center justify-center border-4 border-[#1b1c1c] px-3 font-brand text-sm font-bold shadow-brutal-xs transition-all <?= $page === $currentPage ? 'bg-[#bb0509] text-white' : 'bg-[#fbf9f8] hover:bg-[#dbff5c]' ?>">
                        <?= $page ?>
                    </a>
                    <?php endfor; ?>
                    <a href="<?= htmlspecialchars($pageUrl(min($totalPages, $currentPage + 1))) ?>#games"
                       onclick="<?= htmlspecialchars($paginationClick, ENT_QUOTES, 'UTF-8') ?>"
                       class="pagination-link inline-flex h-10 min-w-10 items-center justify-center border-4 border-[#1b1c1c] bg-[#fbf9f8] px-3 font-brand text-[10px] font-bold uppercase tracking-[.12em] shadow-brutal-xs transition-all hover:bg-[#e4e2e1] <?= $currentPage >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>">
                        →
                    </a>
                </nav>
                <?php endif; ?>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../shares/siteFooter.php'; ?>

<script>
// ── MODAL ──
function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('active');
    document.body.style.overflow = '';
}
function closeModalOutside(e, id) {
    if (e.target.id === id) closeModal(id);
}

// AJAX filters keep the shopper exactly where they are in the inventory.
const filterForm = document.getElementById('product-filter-form');
const filterReset = document.getElementById('product-filter-reset');
let revealObserver = null;

function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    const button = document.getElementById('mobile-menu-button');
    if (!menu) return;
    const isOpen = menu.classList.toggle('open');
    if (button) button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

function toggleMobileFilters() {
    const shell = document.getElementById('product-filter-shell');
    if (!shell) return;
    shell.classList.toggle('open');
}

function setupMotionReveal(scope = document) {
    const targets = scope.querySelectorAll('.game-card, #product-filter-form, .hero-console-frame, .hero-title');
    targets.forEach((target, index) => {
        target.classList.add('motion-reveal');
        target.dataset.revealDelay = String(Math.min(index % 4, 3));
    });

    if (!('IntersectionObserver' in window)) {
        targets.forEach(target => target.classList.add('is-visible'));
        return;
    }

    if (!revealObserver) {
        revealObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    }

    targets.forEach(target => {
        if (!target.classList.contains('is-visible')) {
            revealObserver.observe(target);
        }
    });
}

async function loadFilteredProducts(url, pushState = true) {
    const currentResults = document.getElementById('product-results');
    if (currentResults) currentResults.classList.add('is-loading');

    let html = '';
    try {
        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        html = await response.text();
    } catch (error) {
        if (currentResults) currentResults.classList.remove('is-loading');
        return;
    }
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const nextResults = doc.getElementById('product-results');
    const nextCount = doc.getElementById('product-count');
    const nextActive = doc.getElementById('active-filters');
    const currentCount = document.getElementById('product-count');
    const currentActive = document.getElementById('active-filters');

    if (!nextResults || !currentResults) {
        window.location.href = url;
        return;
    }

    currentResults.innerHTML = nextResults.innerHTML;
    setupMotionReveal(currentResults);
    requestAnimationFrame(() => currentResults.classList.remove('is-loading'));
    if (nextCount && currentCount) currentCount.innerHTML = nextCount.innerHTML;
    if (nextActive && currentActive) {
        currentActive.innerHTML = nextActive.innerHTML;
        currentActive.className = nextActive.className;
    }

    if (pushState) {
        history.pushState({ pixelVaultFilters: true }, '', url + '#games');
    }
}

function updateFilterSummaries() {
    const form = document.getElementById('product-filter-form');
    if (!form) return;

    form.querySelectorAll('.filter-accordion').forEach(details => {
        const field = details.dataset.filterField;
        const checked = form.querySelector(`input[name="${field}"]:checked`);
        const labelWrap = details.querySelector('summary > span');
        if (!field || !checked || !labelWrap) return;

        let current = details.querySelector('.filter-current');
        if (checked.value === '') {
            if (current) current.remove();
            return;
        }

        if (!current) {
            current = document.createElement('span');
            current.className = 'filter-current mt-1 block truncate text-[11px] font-bold text-[#bb0509]';
            labelWrap.appendChild(current);
        }
        current.textContent = checked.value;
    });
}

if (filterForm) {
    filterForm.addEventListener('submit', event => {
        event.preventDefault();
        const currentScrollY = window.scrollY;
        const params = new URLSearchParams(new FormData(filterForm));
        params.delete('page');
        const targetUrl = filterForm.action + '?' + params.toString();
        updateFilterSummaries();
        loadFilteredProducts(targetUrl).finally(() => window.scrollTo({ top: currentScrollY, left: 0 }));
    });
}

document.addEventListener('click', event => {
    const link = event.target.closest('.pagination-link');
    if (!link) return;

    event.preventDefault();
    event.stopPropagation();
    const targetUrl = link.href.replace(/#.*$/, '');
    loadFilteredProducts(targetUrl).finally(() => {
        document.getElementById('games')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}, true);

if (filterReset && filterForm) {
    filterReset.addEventListener('click', event => {
        event.preventDefault();
        const currentScrollY = window.scrollY;
        filterForm.reset();
        filterForm.querySelectorAll('input[type="radio"][value=""]').forEach(input => { input.checked = true; });
        filterForm.querySelector('input[name="q"]').value = '';
        filterForm.querySelectorAll('.filter-current').forEach(item => item.remove());
        filterForm.querySelectorAll('.filter-accordion').forEach(item => item.removeAttribute('open'));
        loadFilteredProducts(filterReset.href).finally(() => window.scrollTo({ top: currentScrollY, left: 0 }));
    });
}

window.addEventListener('popstate', () => {
    loadFilteredProducts(window.location.pathname + window.location.search, false);
});

// ── CART ──
let cart = [];
try { cart = JSON.parse(localStorage.getItem('pv_cart') || '[]'); } catch(e) {}

function normalizeCartText() {
    const names = {
        'Huyen thoai Pixel 1989': 'Huyền thoại Pixel 1989',
        'Vu tru Pha Le': 'Vũ trụ Pha Lê',
        'Thanh pho Pixel': 'Thành phố Pixel',
        'Phao dai 8-bit': 'Pháo đài 8-bit',
    };

    let changed = false;
    cart = cart.map(item => {
        if (names[item.name]) {
            changed = true;
            return { ...item, name: names[item.name] };
        }
        return item;
    });

    if (changed) saveCart();
}

function saveCart() {
    try { localStorage.setItem('pv_cart', JSON.stringify(cart)); } catch(e) {}
}

function renderCart() {
    const itemsEl = document.getElementById('cart-items');
    const totalEl = document.getElementById('cart-total');
    const badgeEl = document.getElementById('cart-badge');
    const messageEl = document.getElementById('cart-message');

    const count = cart.reduce((s, i) => s + i.qty, 0);

    if (cart.length === 0) {
        itemsEl.innerHTML = `<div id="cart-empty" class="cart-empty-state text-center py-16">
            <span class="material-symbols-outlined text-[#cac6bc] block mb-4" style="font-size:56px">sports_esports</span>
            <p class="font-bold text-[#1b1c1c] uppercase text-sm tracking-widest mb-1 leading-relaxed">Giỏ hàng trống!</p>
            <p class="text-[#49473f] text-sm leading-relaxed">Thêm game vào giỏ để bắt đầu</p>
        </div>`;
        if (badgeEl) {
            badgeEl.classList.add('hidden');
            badgeEl.style.display = '';
        }
        totalEl.textContent = '0đ';
        if (messageEl && !messageEl.dataset.keep) {
            messageEl.classList.add('hidden');
            messageEl.textContent = '';
        }
        return;
    }

    if (badgeEl) {
        badgeEl.classList.remove('hidden');
        badgeEl.style.display = 'flex';
        badgeEl.textContent = count;
    }

    let total = 0;
    const rows = cart.map(item => {
        total += item.price * item.qty;
        const imageHtml = item.image
            ? `<img src="${item.image}" alt="" style="width:100%;height:100%;object-fit:contain;">`
            : `<span style="font-family:'Space Mono',monospace;font-size:10px;font-weight:700;color:#526600">PX</span>`;

        return `<div class="flex items-start gap-3 p-3 border-2 border-[#e4e2e1] bg-white">
            <div class="w-14 h-14 bg-[#b2d42f] border-2 border-[#1b1c1c] flex items-center justify-center shrink-0 overflow-hidden p-1">
                ${imageHtml}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-sm truncate">${item.name}</p>
                <p style="color:#bb0509;font-weight:700;font-size:0.875rem">${item.price.toLocaleString('vi-VN')}đ</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                <button onclick="changeQty(${item.id},-1)" style="width:24px;height:24px;border:2px solid #1b1c1c;font-weight:700;display:flex;align-items:center;justify-content:center;font-size:16px;cursor:pointer;">−</button>
                <span style="font-family:'Space Mono',monospace;font-weight:700;font-size:0.875rem;width:18px;text-align:center">${item.qty}</span>
                <button onclick="changeQty(${item.id}, 1)" style="width:24px;height:24px;border:2px solid #1b1c1c;font-weight:700;display:flex;align-items:center;justify-content:center;font-size:16px;cursor:pointer;">+</button>
            </div>
        </div>`;
    });

    itemsEl.innerHTML = rows.join('');
    totalEl.textContent = total.toLocaleString('vi-VN') + 'đ';
}

function addToCart(id, name, price, image = '') {
    const existing = cart.find(i => i.id === id);
    if (existing) {
        existing.qty++;
        if (image && !existing.image) existing.image = image;
    } else {
        cart.push({ id, name, price, image, qty: 1 });
    }
    const messageEl = document.getElementById('cart-message');
    if (messageEl) {
        delete messageEl.dataset.keep;
        messageEl.classList.add('hidden');
        messageEl.textContent = '';
    }
    saveCart(); renderCart(); openCart();
}

function changeQty(id, delta) {
    const idx = cart.findIndex(i => i.id === id);
    if (idx === -1) return;
    cart[idx].qty += delta;
    if (cart[idx].qty <= 0) cart.splice(idx, 1);
    saveCart(); renderCart();
}

function clearCart() {
    if (cart.length && confirm('Xóa toàn bộ giỏ hàng?')) {
        cart = []; saveCart(); renderCart();
    }
}

function checkoutCart() {
    const messageEl = document.getElementById('cart-message');
    if (!cart.length) {
        if (messageEl) {
            messageEl.textContent = 'Giỏ hàng đang trống. Hãy thêm game trước khi thanh toán.';
            messageEl.dataset.keep = '1';
            messageEl.classList.remove('hidden');
        }
        return;
    }

    const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
    const orderId = 'PV' + Date.now().toString().slice(-6);
    cart = [];
    saveCart();
    if (messageEl) {
        messageEl.textContent = `Đặt hàng thành công ${orderId}. Tổng tiền ${total.toLocaleString('vi-VN')}đ.`;
        messageEl.dataset.keep = '1';
        messageEl.classList.remove('hidden');
    }
    renderCart();
}

function openCart() {
    document.getElementById('cart-panel').classList.add('open');
    document.getElementById('cart-overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeCart() {
    document.getElementById('cart-panel').classList.remove('open');
    document.getElementById('cart-overlay').classList.remove('open');
    document.body.style.overflow = '';
}

const modalByHash = {
    '#about': 'modal-about',
    '#leaderboard': 'modal-leaderboard',
    '#policy': 'modal-policy',
    '#terms': 'modal-terms',
    '#support': 'modal-support',
};

setupMotionReveal();
normalizeCartText();
renderCart();
if (modalByHash[window.location.hash]) {
    openModal(modalByHash[window.location.hash]);
}
</script>

</body>
</html>

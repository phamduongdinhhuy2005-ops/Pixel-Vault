<?php 
$products = $products ?? [];
$categories = $categories ?? [];
$total    = count($products);
$revenue  = array_sum(array_map(fn($p) => $p->getPrice(), $products));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIXEL VAULT – Bảng Quản Trị</title>
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

        /* Dark dot-matrix for admin */
        .dot-matrix-dark {
            background-color: #0d0d0d;
            background-image: radial-gradient(#1e1e1e 1px, transparent 1px);
            background-size: 8px 8px;
        }
        .scanlines {
            background: linear-gradient(to bottom, rgba(255,255,255,0) 50%, rgba(0,0,0,0.2) 50%);
            background-size: 100% 4px; pointer-events: none;
        }

        /* ── STAT CARD SHADOWS — coloured per card ── */
        .shadow-red   { box-shadow: 6px 6px 0px 0px #bb0509; }
        .shadow-green { box-shadow: 6px 6px 0px 0px #526600; }
        .shadow-zinc  { box-shadow: 6px 6px 0px 0px #3f3f46; }

        .btn-press { transition: transform .12s ease, box-shadow .12s ease; }
        .btn-press:hover  { transform: translate(-2px,-2px); }
        .btn-press:active { transform: translate(2px, 2px); }

        .nav-link { transition: transform .12s ease; }
        .nav-link:hover { transform: translate(1px,-1px); }

        /* Blinking cursor */
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }
        .blink { animation: blink 1s step-end infinite; }

        /* Table row hover */
        .admin-row { transition: background .12s ease; }
        .admin-row:hover { background: rgba(187,5,9,0.08); }
        .meter-track {
            height: 14px;
            background: #111;
            border: 2px solid #52525b;
            display: flex;
            overflow: hidden;
        }
        .meter-fill {
            height: 100%;
            border-right: 2px solid #0d0d0d;
        }
        .meter-rest {
            flex: 1;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 4px, #3f3f46 4px, #3f3f46 8px);
        }

        /* Ticker */
        @keyframes ticker { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
        .ticker-track { animation: ticker 30s linear infinite; white-space: nowrap; }
    </style>
</head>
<body class="dot-matrix-dark text-white min-h-screen">

<!-- ─── TOP BAR ─── -->
<nav class="bg-black border-b-4 border-[#bb0509] shadow-[0px_4px_0px_0px_#bb0509] sticky top-0 z-50">

    <!-- Ticker -->
    <div class="bg-[#bb0509] text-white overflow-hidden py-1">
        <div class="ticker-track inline-block font-brand text-[9px] font-bold uppercase tracking-[.15em]">
            &nbsp;&nbsp;⚡ PIXEL VAULT ADMIN &nbsp;·&nbsp; QUẢN LÝ KHO HÀNG &nbsp;·&nbsp; HỆ THỐNG ĐANG HOẠT ĐỘNG &nbsp;·&nbsp; <?= $total ?> TỰA GAME TRONG KHO &nbsp;·&nbsp; TỔNG GIÁ TRỊ: <?= number_format($revenue, 0, ',', '.') ?>đ &nbsp;&nbsp;⚡ PIXEL VAULT ADMIN &nbsp;·&nbsp; QUẢN LÝ KHO HÀNG &nbsp;·&nbsp; HỆ THỐNG ĐANG HOẠT ĐỘNG &nbsp;·&nbsp; <?= $total ?> TỰA GAME TRONG KHO &nbsp;·&nbsp; TỔNG GIÁ TRỊ: <?= number_format($revenue, 0, ',', '.') ?>đ &nbsp;&nbsp;
        </div>
    </div>

    <!-- Main row -->
    <div class="max-w-[1400px] mx-auto px-4 md:px-12 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="<?= url() ?>" class="font-brand text-xl font-bold italic text-[#bb0509] tracking-tighter">PIXEL_VAULT</a>
            <div class="hidden md:flex items-center gap-2 border-l-2 border-zinc-700 pl-4">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[10px] font-bold uppercase tracking-[.12em] text-green-500">HỆ THỐNG HOẠT ĐỘNG</span>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <span class="hidden md:block font-brand text-[10px] text-zinc-500 uppercase tracking-widest">
                ADMIN PANEL · v1.0.0
            </span>
            <a href="<?= url() ?>" class="nav-link flex items-center gap-2 text-[11px] font-bold uppercase tracking-[.12em] text-zinc-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined" style="font-size:16px">storefront</span>
                VỀ CỬA HÀNG
            </a>
        </div>
    </div>
</nav>

<main class="max-w-[1400px] mx-auto px-4 md:px-12 py-8 space-y-8">

    <!-- ─── PAGE HEADER ─── -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <p class="font-brand text-[10px] text-green-500/60 uppercase tracking-[.15em] mb-2">
                > ĐĂNG NHẬP: QUẢN_TRỊ_VIÊN<span class="blink">_</span>
            </p>
            <h1 class="font-brand text-3xl md:text-4xl font-bold text-white uppercase tracking-tight">
                BẢNG QUẢN TRỊ
            </h1>
            <p class="text-sm text-zinc-500 mt-1">PIXEL VAULT — Hệ Thống Quản Lý Kho Game</p>
        </div>
        <a href="<?= url('Product/add') ?>"
           class="btn-press inline-flex items-center gap-2 bg-[#bb0509] text-white px-6 py-4 border-4 border-white shadow-[8px_8px_0_#bb0509] text-[11px] font-bold uppercase tracking-[.12em] hover:brightness-110 transition-all">
            <span class="material-symbols-outlined" style="font-size:18px">add_circle</span>
            THÊM GAME MỚI
        </a>
    </div>

    <?php if (!empty($_SESSION['flash_success']) || !empty($_SESSION['flash_error'])): ?>
    <div class="<?= !empty($_SESSION['flash_error']) ? 'border-[#bb0509] text-[#ffb4a9]' : 'border-[#526600] text-[#dbff5c]' ?> bg-black border-4 px-5 py-4 font-brand text-[11px] font-bold uppercase tracking-[.12em]">
        <?= htmlspecialchars($_SESSION['flash_error'] ?? $_SESSION['flash_success']) ?>
    </div>
    <?php unset($_SESSION['flash_success'], $_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- ─── STAT CARDS ─── -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        <!-- Total games — RED -->
        <div class="bg-black border-4 border-[#bb0509] shadow-red p-6 relative overflow-hidden">
            <div class="absolute inset-0 scanlines opacity-30"></div>
            <div class="absolute top-0 left-0 right-0 h-1 bg-[#bb0509]"></div>
            <p class="text-[10px] font-bold uppercase tracking-[.15em] text-[#bb0509]/70 mb-2">TỔNG GAME</p>
            <p class="font-brand text-5xl font-bold text-white"><?= $total ?></p>
            <p class="text-[10px] text-zinc-600 mt-2 uppercase tracking-widest">tựa game trong kho</p>
            <span class="material-symbols-outlined absolute bottom-4 right-4 text-[#bb0509]/15"
                  style="font-size:72px;font-variation-settings:'FILL' 1">sports_esports</span>
        </div>

        <!-- Revenue — GREEN -->
        <div class="bg-black border-4 border-[#526600] shadow-green p-6 relative overflow-hidden">
            <div class="absolute inset-0 scanlines opacity-30"></div>
            <div class="absolute top-0 left-0 right-0 h-1 bg-[#526600]"></div>
            <p class="text-[10px] font-bold uppercase tracking-[.15em] text-[#b2d42f]/70 mb-2">TỔNG TRỊ GIÁ KHO</p>
            <p class="font-brand text-3xl font-bold text-[#b2d42f]"><?= number_format($revenue, 0, ',', '.') ?>đ</p>
            <p class="text-[10px] text-zinc-600 mt-2 uppercase tracking-widest">tổng giá trị hàng tồn</p>
            <span class="material-symbols-outlined absolute bottom-4 right-4 text-[#526600]/20"
                  style="font-size:72px;font-variation-settings:'FILL' 1">payments</span>
        </div>

        <!-- Avg price — ZINC (accent line yellow-green) -->
        <div class="bg-black border-4 border-zinc-600 shadow-zinc p-6 relative overflow-hidden">
            <div class="absolute inset-0 scanlines opacity-30"></div>
            <div class="absolute top-0 left-0 right-0 h-1 bg-[#dbff5c]"></div>
            <p class="text-[10px] font-bold uppercase tracking-[.15em] text-zinc-500 mb-2">GIÁ TRUNG BÌNH</p>
            <p class="font-brand text-3xl font-bold text-zinc-200">
                <?= $total > 0 ? number_format($revenue / $total, 0, ',', '.') . 'đ' : '—' ?>
            </p>
            <p class="text-[10px] text-zinc-600 mt-2 uppercase tracking-widest">mỗi tựa game</p>
            <span class="material-symbols-outlined absolute bottom-4 right-4 text-zinc-800"
                  style="font-size:72px;font-variation-settings:'FILL' 1">analytics</span>
        </div>

    </div>

    <!-- ─── MAIN CONTENT ─── -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Product table (2/3) -->
        <div class="lg:col-span-2 bg-black border-4 border-[#bb0509]">

            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-[#bb0509]">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[#bb0509]" style="font-variation-settings:'FILL' 1">inventory_2</span>
                    <span class="text-white text-[11px] font-bold uppercase tracking-[.12em]">DANH SÁCH GAME</span>
                </div>
                <span class="bg-[#bb0509] text-white text-[10px] font-bold px-3 py-1 font-brand"><?= $total ?> tựa</span>
            </div>

            <?php if (empty($products)): ?>
            <div class="p-12 text-center">
                <span class="material-symbols-outlined text-zinc-700 block mb-4" style="font-size:64px">inventory</span>
                <p class="text-zinc-500 text-sm">Kho game đang trống.</p>
                <a href="<?= url('Product/add') ?>" class="inline-block mt-4 bg-[#bb0509] text-white px-6 py-3 border-2 border-white/20 text-[11px] font-bold uppercase tracking-[.12em] hover:brightness-110 transition-all">
                    THÊM GAME ĐẦU TIÊN
                </a>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-zinc-800">
                            <th class="text-left px-6 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-zinc-500">ID</th>
                            <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-zinc-500">ẢNH</th>
                            <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-zinc-500">TÊN GAME</th>
                            <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-zinc-500">GIÁ</th>
                            <th class="text-center px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-zinc-500">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900">
                        <?php foreach ($products as $p): ?>
                        <?php $primaryImage = $p->getPrimaryImage(); ?>
                        <tr class="admin-row">
                            <td class="px-6 py-4">
                                <span class="font-brand text-[#bb0509] text-xs font-bold">#<?= str_pad($p->getID(), 3, '0', STR_PAD_LEFT) ?></span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="w-14 h-14 border-2 border-zinc-700 bg-[#b2d42f] flex items-center justify-center overflow-hidden">
                                    <?php if ($primaryImage): ?>
                                    <img src="<?= htmlspecialchars($primaryImage) ?>" alt="<?= htmlspecialchars($p->getName()) ?>" class="w-full h-full object-contain opacity-100">
                                    <?php else: ?>
                                    <span class="font-brand text-xs font-bold text-[#526600]/60">PX</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-white truncate max-w-[200px]"><?= htmlspecialchars($p->getName()) ?></div>
                                <?php if ($p->getDescription()): ?>
                                <div class="text-[11px] text-zinc-600 truncate max-w-[200px] mt-0.5">
                                    <?= htmlspecialchars(substr($p->getDescription(), 0, 60)) ?>...
                                </div>
                                <?php endif; ?>
                                <?php if (count($p->getImages()) > 0): ?>
                                <div class="text-[10px] text-[#dbff5c] font-brand mt-1"><?= count($p->getImages()) ?> ảnh</div>
                                <?php endif; ?>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <span class="border border-[#bb0509] px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-[#ffb4a9]"><?= htmlspecialchars($p->getCategory()) ?></span>
                                    <?php foreach ($p->getGenres() as $genre): ?>
                                    <span class="border border-[#526600] px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-[#dbff5c]"><?= htmlspecialchars($genre) ?></span>
                                    <?php endforeach; ?>
                                    <span class="border border-zinc-700 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-zinc-400"><?= htmlspecialchars($p->getCondition()) ?></span>
                                    <span class="border border-zinc-700 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-zinc-400"><?= htmlspecialchars($p->getResolution()) ?></span>
                                    <span class="border border-zinc-700 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-zinc-400"><?= htmlspecialchars($p->getRomFormat()) ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <span class="text-[#b2d42f] font-bold font-brand"><?= number_format($p->getPrice(), 0, ',', '.') ?>đ</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex gap-2 justify-center">
                                    <a href="<?= url('Product/detail/' . $p->getID()) ?>" title="Chi tiết"
                                       class="p-2 border-2 border-zinc-700 hover:border-white hover:text-white transition-all text-zinc-500">
                                        <span class="material-symbols-outlined" style="font-size:16px">visibility</span>
                                    </a>
                                    <a href="<?= url('Product/edit/' . $p->getID()) ?>" title="Sửa"
                                       class="p-2 border-2 border-zinc-700 hover:border-[#b2d42f] hover:text-[#b2d42f] transition-all text-zinc-500">
                                        <span class="material-symbols-outlined" style="font-size:16px">edit</span>
                                    </a>
                                    <a href="<?= url('Product/delete/' . $p->getID()) ?>" title="Xóa"
                                       onclick="return confirm('Xóa game «<?= htmlspecialchars($p->getName()) ?>»?')"
                                       class="p-2 border-2 border-zinc-700 hover:border-[#bb0509] hover:text-[#bb0509] transition-all text-zinc-500">
                                        <span class="material-symbols-outlined" style="font-size:16px">delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar (1/3) -->
        <div class="space-y-5">

            <!-- Quick actions -->
            <div class="bg-black border-4 border-zinc-700">
                <div class="px-6 py-4 border-b-4 border-zinc-700 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#dbff5c]" style="font-variation-settings:'FILL' 1;font-size:16px">flash_on</span>
                    <span class="text-[11px] font-bold uppercase tracking-[.12em] text-zinc-300">THAO TÁC NHANH</span>
                </div>
                <div class="p-4 space-y-3">
                    <a href="<?= url('Product/add') ?>"
                       class="btn-press flex items-center gap-3 w-full bg-[#bb0509] text-white px-5 py-4 border-2 border-[#bb0509]/40 text-[11px] font-bold uppercase tracking-[.12em] hover:brightness-110 transition-all">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:20px">add_circle</span>
                        Thêm Game Mới
                    </a>
                    <a href="<?= url() ?>"
                       class="flex items-center gap-3 w-full bg-zinc-900 text-zinc-300 px-5 py-4 border-2 border-zinc-700 text-[11px] font-bold uppercase tracking-[.12em] hover:bg-zinc-800 hover:text-white transition-all">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:20px">storefront</span>
                        Xem Cửa Hàng
                    </a>
                </div>
            </div>

            <!-- Categories -->
            <div id="categories" class="bg-black border-4 border-[#526600] shadow-green">
                <div class="px-6 py-4 border-b-4 border-[#526600] flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#dbff5c]" style="font-variation-settings:'FILL' 1;font-size:16px">category</span>
                        <span class="text-[11px] font-bold uppercase tracking-[.12em] text-[#dbff5c]">DANH MỤC</span>
                    </div>
                    <span class="bg-[#526600] px-2 py-1 font-brand text-[10px] font-bold text-[#dbff5c]"><?= count($categories) ?></span>
                </div>
                <div class="p-4 space-y-4">
                    <form method="POST" action="<?= url('Category/add') ?>" class="space-y-3 border-2 border-zinc-800 bg-zinc-950 p-3">
                        <input type="text" name="name" placeholder="Tên danh mục mới"
                               class="w-full border-2 border-zinc-700 bg-black px-3 py-2 text-sm font-semibold text-white placeholder:text-zinc-600 focus:border-[#dbff5c] focus:ring-0"
                               required>
                        <textarea name="description" rows="2" placeholder="Mô tả danh mục"
                                  class="w-full resize-none border-2 border-zinc-700 bg-black px-3 py-2 text-sm text-zinc-200 placeholder:text-zinc-600 focus:border-[#dbff5c] focus:ring-0"></textarea>
                        <button class="btn-press flex w-full items-center justify-center gap-2 bg-[#526600] px-4 py-3 text-[10px] font-bold uppercase tracking-[.12em] text-[#dbff5c] hover:brightness-125">
                            <span class="material-symbols-outlined" style="font-size:16px">add</span>
                            Thêm danh mục
                        </button>
                    </form>

                    <?php if (empty($categories)): ?>
                    <p class="border-2 border-zinc-800 p-4 text-sm text-zinc-500">Chưa có danh mục nào.</p>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($categories as $category): ?>
                        <form method="POST" action="<?= url('Category/edit/' . (int) $category['id']) ?>" class="space-y-2 border-2 border-zinc-800 bg-zinc-950 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-brand text-[10px] font-bold text-[#dbff5c]">#<?= str_pad((int) $category['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                <span class="text-[10px] font-bold uppercase tracking-[.12em] text-zinc-500"><?= (int) $category['product_count'] ?> game</span>
                            </div>
                            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>"
                                   class="w-full border-2 border-zinc-700 bg-black px-3 py-2 text-sm font-semibold text-white focus:border-[#dbff5c] focus:ring-0"
                                   required>
                            <textarea name="description" rows="2"
                                      class="w-full resize-none border-2 border-zinc-700 bg-black px-3 py-2 text-xs text-zinc-300 focus:border-[#dbff5c] focus:ring-0"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                            <div class="grid grid-cols-2 gap-2">
                                <button class="border-2 border-[#526600] px-3 py-2 text-[10px] font-bold uppercase tracking-[.12em] text-[#dbff5c] hover:bg-[#526600]">
                                    Lưu
                                </button>
                                <a href="<?= url('Category/delete/' . (int) $category['id']) ?>"
                                   onclick="return confirm('Xóa danh mục «<?= htmlspecialchars($category['name']) ?>»? Các game thuộc danh mục này sẽ thành chưa phân loại.')"
                                   class="border-2 border-[#bb0509] px-3 py-2 text-center text-[10px] font-bold uppercase tracking-[.12em] text-[#ffb4a9] hover:bg-[#bb0509] hover:text-white">
                                    Xóa
                                </a>
                            </div>
                        </form>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- System log -->
            <div class="bg-black border-4 border-[#526600] shadow-green relative overflow-hidden">
                <div class="absolute inset-0 scanlines opacity-20"></div>
                <div class="relative px-6 py-4 border-b-4 border-[#526600] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#dbff5c]" style="font-variation-settings:'FILL' 1;font-size:16px">memory</span>
                    <span class="text-[11px] font-bold uppercase tracking-[.12em] text-[#dbff5c]">SYS_MONITOR</span>
                </div>
                <div class="relative p-4 space-y-4">
                    <?php
                    $cpu = min(96, 35 + ($total * 9));
                    $memory = min(98, 42 + ($total * 7));
                    foreach ([['CPU USAGE', $cpu, 'bg-[#dbff5c]', 'text-[#dbff5c]'], ['MEMORY', $memory, 'bg-[#bb0509]', 'text-[#ffb4a9]']] as [$label, $value, $bar, $textClass]):
                    ?>
                    <div>
                        <div class="flex justify-between font-brand text-[10px] font-bold uppercase tracking-[.12em] text-zinc-300 mb-2">
                            <span><?= $label ?></span>
                            <span class="<?= $textClass ?>"><?= $value ?>%</span>
                        </div>
                        <div class="meter-track">
                            <div class="meter-fill <?= $bar ?>" style="width:<?= $value ?>%"></div>
                            <div class="meter-rest"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- System log -->
            <div class="bg-black border-4 border-zinc-700">
                <div class="px-6 py-4 border-b-4 border-zinc-700 flex items-center gap-2">
                    <span class="material-symbols-outlined text-zinc-500" style="font-size:16px">terminal</span>
                    <span class="text-[11px] font-bold uppercase tracking-[.12em] text-zinc-400">NHẬT KÝ HỆ THỐNG</span>
                </div>
                <div class="p-4 space-y-2 font-brand text-[10px]">
                    <p class="text-green-500"><span class="text-zinc-600">[OK]</span> Kết nối DB: THÀNH CÔNG</p>
                    <p class="text-green-500"><span class="text-zinc-600">[OK]</span> Tải <?= $total ?> bản ghi</p>
                    <p class="text-[#b2d42f]"><span class="text-zinc-600">[SYS]</span> Phiên bản: v1.0.0</p>
                    <p class="text-zinc-600"><span class="text-zinc-700">[---]</span> Chờ lệnh...</p>
                    <p class="text-green-400 flex items-center gap-1">> HỆ THỐNG SẴN SÀNG<span class="blink font-brand">_</span></p>
                </div>
            </div>

            <!-- Top prices mini-chart -->
            <?php if (!empty($products)): ?>
            <div class="bg-black border-4 border-zinc-700">
                <div class="px-6 py-4 border-b-4 border-zinc-700 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#b2d42f]" style="font-variation-settings:'FILL' 1;font-size:16px">bar_chart</span>
                    <span class="text-[11px] font-bold uppercase tracking-[.12em] text-zinc-400">TOP GIÁ CAO NHẤT</span>
                </div>
                <div class="p-4 space-y-3">
                    <?php
                    $sorted = $products;
                    usort($sorted, fn($a,$b) => $b->getPrice() - $a->getPrice());
                    $top3   = array_slice($sorted, 0, 3);
                    $maxP   = $top3[0]->getPrice();
                    $barColors = ['bg-[#bb0509]', 'bg-[#b2d42f]', 'bg-zinc-600'];
                    foreach ($top3 as $i => $p):
                        $pct = $maxP > 0 ? round(($p->getPrice() / $maxP) * 100) : 0;
                    ?>
                    <div>
                        <div class="flex justify-between text-[10px] mb-1.5">
                            <span class="text-zinc-400 truncate max-w-[140px]"><?= htmlspecialchars($p->getName()) ?></span>
                            <span class="text-white font-bold font-brand"><?= number_format($p->getPrice(), 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="h-2 bg-zinc-900 border border-zinc-800 relative">
                            <div class="h-full <?= $barColors[$i] ?> transition-all" style="width:<?= $pct ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<!-- ─── FOOTER ─── -->
<footer class="border-t-4 border-[#bb0509] bg-black mt-12">
    <div class="max-w-[1400px] mx-auto px-4 md:px-12 py-8 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
        <div>
            <div class="font-brand text-lg font-bold italic text-[#bb0509]">PIXEL_VAULT</div>
            <div class="font-brand text-[10px] text-zinc-700 mt-1 uppercase tracking-widest">ADMIN PANEL</div>
        </div>
        <div class="text-center">
            <div class="flex items-center justify-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="font-brand text-[10px] text-zinc-500 uppercase tracking-widest">HỆ THỐNG ĐANG HOẠT ĐỘNG</span>
            </div>
            <div class="font-brand text-[9px] text-zinc-700">ĐĂNG NHẬP: QUẢN_TRỊ_VIÊN</div>
        </div>
        <div class="font-brand text-[10px] text-zinc-700 md:text-right">© 1989–2026 PIXEL VAULT</div>
    </div>
</footer>

</body>
</html>

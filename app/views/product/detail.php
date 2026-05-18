<?php
if (!isset($product)) die('Không tìm thấy game!');
$images = $product->getImages();
$primaryImage = $product->getPrimaryImage();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIXEL VAULT – <?= htmlspecialchars($product->getName()) ?></title>
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
        .dot-matrix { background-image: radial-gradient(#cac6bc 1px, transparent 1px); background-size: 8px 8px; }
        .scanlines {
            background: linear-gradient(to bottom, rgba(255,255,255,0) 50%, rgba(0,0,0,0.06) 50%);
            background-size: 100% 4px; pointer-events: none;
        }
        .shadow-brutal    { box-shadow: 8px 8px 0px 0px #1b1c1c; }
        .shadow-brutal-sm { box-shadow: 4px 4px 0px 0px #1b1c1c; }
        .shadow-brutal-lg { box-shadow: 12px 12px 0px 0px #1b1c1c; }
        .shadow-brutal-md { box-shadow: 6px 6px 0px 0px #1b1c1c; }
        .btn-press { transition: transform .12s ease, box-shadow .12s ease; }
        .btn-press:hover  { transform: translate(-2px,-2px); box-shadow: 8px 8px 0 #1b1c1c; }
        .btn-press:active { transform: translate(2px, 2px);  box-shadow: 2px 2px 0 #1b1c1c; }
        .nav-link { transition: transform .12s ease; }
        .nav-link:hover  { transform: translate(1px,-1px); }
        .crt-frame {
            background-color: #1b1c1c;
            padding: .5rem;
            box-shadow: 8px 8px 0 #1b1c1c;
        }
        .crt-screen {
            aspect-ratio: 1 / 1;
            background-color: #b2d42f;
            background-image:
                linear-gradient(to bottom, rgba(255,255,255,0) 50%, rgba(0,0,0,.1) 50%),
                radial-gradient(rgba(82,102,0,.22) 1px, transparent 1px);
            background-size: 100% 4px, 8px 8px;
        }
        .credit-box {
            background: #1b1c1c;
            color: #dbff5c;
            text-shadow: 0 0 10px rgba(219,255,92,.45);
        }
        .product-fit-image {
            object-fit: contain;
            background: rgba(251,249,248,.18);
        }
        .product-thumb {
            aspect-ratio: 1 / 1;
            min-height: 120px;
        }
        .thumb-active {
            border-color: #bb0509 !important;
            box-shadow: 4px 4px 0 #bb0509;
        }
    </style>
</head>
<body class="bg-[#fbf9f8] text-[#1b1c1c] dot-matrix min-h-screen">

<!-- ─── NAVIGATION ─── -->
<nav class="bg-[#fbf9f8] border-b-4 border-[#1b1c1c] shadow-[0px_4px_0px_0px_#1b1c1c] sticky top-0 z-50">
    <div class="max-w-[1200px] mx-auto px-4 md:px-12 py-4 flex justify-between items-center">
        <a href="<?= url() ?>" class="font-brand text-2xl font-bold italic text-[#bb0509] tracking-tighter">PIXEL_VAULT</a>
        <a href="<?= url() ?>" class="nav-link flex items-center gap-2 text-[11px] font-bold uppercase tracking-[.12em] text-[#1b1c1c] hover:text-[#bb0509] transition-colors">
            <span class="material-symbols-outlined" style="font-size:16px">arrow_back</span>
            VỀ CỬA HÀNG
        </a>
    </div>
</nav>

<main class="max-w-[1200px] mx-auto px-4 md:px-12 py-12">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 mb-8 text-[11px] font-bold uppercase tracking-[.12em] text-[#49473f]">
        <a href="<?= url() ?>" class="hover:text-[#bb0509] transition-colors">Cửa Hàng</a>
        <span>›</span>
        <span class="text-[#1b1c1c]"><?= htmlspecialchars($product->getName()) ?></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

        <!-- ─── LEFT: IMAGE ─── -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Main image -->
            <div class="crt-frame relative">
                <div class="crt-screen relative w-full flex items-center justify-center overflow-hidden border-2 border-[#cac6bc]">
                    <?php if ($primaryImage): ?>
                    <img id="main-product-image" src="<?= htmlspecialchars($primaryImage) ?>" alt="<?= htmlspecialchars($product->getName()) ?>" class="w-full h-full product-fit-image opacity-100">
                    <?php else: ?>
                    <span class="font-brand text-[132px] font-bold text-[#526600]/25 select-none z-0">PX</span>
                    <?php endif; ?>
                    <div class="absolute inset-0 scanlines z-10"></div>
                    <div class="absolute inset-6 border-2 border-dashed border-[#526600]/70 z-10"></div>
                </div>
                <!-- ID tag -->
                <div class="absolute top-4 left-4 bg-[#1b1c1c] text-white font-brand text-xs px-3 py-1.5 z-20">
                    GAME #<?= str_pad($product->getID(), 3, '0', STR_PAD_LEFT) ?>
                </div>
                <!-- Retro badge -->
                <div class="absolute top-4 right-4 bg-[#bb0509] text-white text-[9px] font-bold uppercase tracking-widest px-2 py-1 border-2 border-[#1b1c1c] shadow-[2px_2px_0_#1b1c1c] z-20 rotate-3">
                    RETRO
                </div>
            </div>

            <!-- Thumbnails row (decorative) -->
            <div class="grid grid-cols-3 gap-3">
                <?php if (!empty($images)): ?>
                <?php foreach (array_slice($images, 0, 3) as $index => $image): ?>
                <button type="button"
                        data-image="<?= htmlspecialchars($image) ?>"
                        class="product-thumb border-4 border-[#1b1c1c] shadow-brutal-sm flex items-center justify-center relative overflow-hidden cursor-pointer hover:-translate-y-1 transition-transform <?= $index === 0 ? 'bg-[#dbff5c] thumb-active' : 'bg-[#e4e2e1]' ?>"
                        aria-label="Xem ảnh <?= $index + 1 ?>">
                    <img src="<?= htmlspecialchars($image) ?>" alt="Ảnh <?= $index + 1 ?> - <?= htmlspecialchars($product->getName()) ?>" class="w-full h-full object-contain opacity-100">
                    <div class="absolute inset-0 scanlines"></div>
                </button>
                <?php endforeach; ?>
                <?php else: ?>
                <?php foreach ([['bg-[#dbff5c]','░░░'], ['bg-[#e4e2e1]','▓▓▓'], ['bg-[#b2d42f]','███']] as [$bg,$c]): ?>
                <div class="<?= $bg ?> border-4 border-[#1b1c1c] shadow-brutal-sm h-20 flex items-center justify-center relative overflow-hidden cursor-pointer hover:-translate-y-1 transition-transform">
                    <div class="absolute inset-0 scanlines"></div>
                    <span class="font-brand text-xl text-[#526600]/30"><?= $c ?></span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ─── RIGHT: INFO ─── -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Title + Price -->
            <div class="space-y-6">
                <div class="inline-block bg-[#dbff5c] text-[#3d4c00] text-[9px] font-bold uppercase tracking-[.12em] px-3 py-1.5 border-2 border-[#1b1c1c] shadow-brutal-sm -rotate-2">
                    CÒN HÀNG · CHÍNH HÃNG
                </div>
                <h1 class="font-brand text-3xl lg:text-5xl font-bold uppercase tracking-tight">
                    <?= htmlspecialchars($product->getName()) ?>
                </h1>
                <div class="credit-box border-4 border-[#1b1c1c] shadow-brutal-md p-4 inline-flex items-end gap-4">
                    <span class="font-brand text-[11px] font-bold uppercase tracking-[.12em] text-[#e4e2e1] pb-1">Credits req:</span>
                    <span class="font-brand text-4xl font-bold tracking-widest">
                        <?= number_format($product->getPrice(), 0, ',', '.') ?>đ
                    </span>
                </div>
                <button onclick='addToCart(<?= $product->getID() ?>, <?= json_encode($product->getName(), JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= $product->getPrice() ?>, <?= json_encode($primaryImage ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                        class="w-full btn-press bg-[#bb0509] text-white py-5 border-4 border-[#1b1c1c] shadow-brutal font-bold uppercase tracking-[.12em] text-sm flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:24px">videogame_asset</span>
                    THÊM VÀO GIỎ HÀNG
                </button>
                <div id="cart-detail-message" class="hidden mt-4 border-2 border-[#526600] bg-[#dbff5c] px-4 py-3 text-[12px] font-bold text-[#3d4c00] leading-relaxed"></div>
            </div>

            <!-- Specs table -->
            <div class="bg-[#eae8e7] border-4 border-[#1b1c1c] shadow-brutal-sm relative mt-10">
                <div class="absolute -top-4 left-6 bg-[#fbf9f8] border-4 border-[#1b1c1c] px-4 py-1 font-brand text-[10px] font-bold uppercase tracking-[.12em]">
                    THÔNG TIN HỆ THỐNG
                </div>
                <div class="divide-y-2 divide-dashed divide-[#cac6bc] pt-6">
                    <?php
                    $specs = [
                        ['LOẠI BĂNG',       $product->getCategory()],
                        ['ĐỊNH DẠNG',       $product->getRomFormat()],
                        ['SỐ NGƯỜI CHƠI',   $product->getPlayers()],
                        ['ĐỘ PHÂN GIẢI',    $product->getResolution() . ' px'],
                        ['KHU VỰC',         $product->getRegion()],
                        ['TÌNH TRẠNG',      $product->getCondition()],
                        ['THỂ LOẠI',        $product->getGenreText() ?: 'Chưa phân loại'],
                    ];
                    foreach ($specs as [$label, $val]):
                    ?>
                    <div class="flex justify-between items-center px-6 py-3">
                        <span class="text-[11px] font-bold uppercase tracking-[.1em] text-[#49473f]"><?= $label ?></span>
                        <span class="text-sm font-semibold text-[#1b1c1c]"><?= $val ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-[#fbf9f8] border-4 border-[#1b1c1c] shadow-brutal-sm">
                <div class="bg-[#dbff5c] border-b-4 border-[#1b1c1c] px-6 py-3 text-[11px] font-bold uppercase tracking-[.12em] text-[#3d4c00]">
                    MÔ TẢ GAME
                </div>
                <div class="p-6">
                    <p class="text-[#49473f] leading-relaxed">
                        <?= nl2br(htmlspecialchars($product->getDescription() ?: 'Tựa game retro kinh điển với đồ họa pixel độc đáo và gameplay hấp dẫn không thể bỏ lỡ. Đây là một phần không thể thiếu trong bộ sưu tập của mọi game thủ yêu retro.')) ?>
                    </p>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex gap-4">
                <a href="<?= url('Product/edit/' . $product->getID()) ?>"
                   class="flex-1 text-center py-4 border-4 border-[#1b1c1c] text-[11px] font-bold uppercase tracking-[.12em] hover:bg-[#dbff5c] transition-all shadow-brutal-sm">
                    ✏ SỬA GAME
                </a>
                <a href="<?= url() ?>"
                   class="flex-1 text-center py-4 border-4 border-[#1b1c1c] text-[11px] font-bold uppercase tracking-[.12em] hover:bg-[#e4e2e1] transition-all shadow-brutal-sm">
                    ← VỀ CỬA HÀNG
                </a>
            </div>

        </div>
    </div>
</main>

<!-- ─── FOOTER ─── -->
<footer class="bg-[#e4e2e1] border-t-4 border-[#1b1c1c] mt-24">
    <div class="max-w-[1200px] mx-auto px-4 md:px-12 py-10 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="font-brand text-lg font-bold italic text-[#1b1c1c]">PIXEL_VAULT</div>
        <div class="flex flex-wrap justify-center gap-6 text-[10px] font-bold uppercase tracking-[.12em]">
            <a href="<?= url() ?>#policy" class="text-[#49473f] hover:text-[#526600] underline transition-colors">Chính Sách</a>
            <a href="<?= url() ?>#terms" class="text-[#49473f] hover:text-[#526600] underline transition-colors">Điều Khoản</a>
            <a href="<?= url() ?>#support" class="text-[#49473f] hover:text-[#526600] underline transition-colors">Hỗ Trợ</a>
        </div>
        <div class="text-[10px] text-[#49473f] text-center md:text-right font-medium">
            © 1989–2026 PIXEL VAULT. MỌI QUYỀN ĐƯỢC BẢO LƯU.
        </div>
    </div>
</footer>

<script>
function selectProductImage(button, image) {
    const mainImage = document.getElementById('main-product-image');
    if (!mainImage) return;

    mainImage.src = image;
    document.querySelectorAll('.product-thumb').forEach(item => item.classList.remove('thumb-active'));
    button.classList.add('thumb-active');
}

document.querySelectorAll('.product-thumb[data-image]').forEach(button => {
    button.addEventListener('click', () => {
        selectProductImage(button, button.dataset.image || '');
    });
});

function addToCart(id, name, price, image = '') {
    let cart = [];
    try { cart = JSON.parse(localStorage.getItem('pv_cart') || '[]'); } catch(e) {}
    const names = {
        'Huyen thoai Pixel 1989': 'Huyền thoại Pixel 1989',
        'Vu tru Pha Le': 'Vũ trụ Pha Lê',
        'Thanh pho Pixel': 'Thành phố Pixel',
        'Phao dai 8-bit': 'Pháo đài 8-bit',
    };

    cart = cart.map(item => names[item.name] ? { ...item, name: names[item.name] } : item);

    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty++;
        if (image && !existing.image) existing.image = image;
    } else {
        cart.push({ id, name, price, image, qty: 1 });
    }

    localStorage.setItem('pv_cart', JSON.stringify(cart));

    const message = document.getElementById('cart-detail-message');
    if (message) {
        const count = cart.reduce((sum, item) => sum + item.qty, 0);
        message.textContent = `Đã thêm vào giỏ hàng. Hiện có ${count} sản phẩm.`;
        message.classList.remove('hidden');
    }
}
</script>
</body>
</html>

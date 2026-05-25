<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIXEL VAULT – Thêm Game Mới</title>
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
        button, a, .btn-press, .nav-link { line-height: 1.35; }
        .dot-matrix { background-image: radial-gradient(#cac6bc 1px, transparent 1px); background-size: 8px 8px; }
        .shadow-brutal    { box-shadow: 8px 8px 0px 0px #1b1c1c; }
        .shadow-brutal-sm { box-shadow: 4px 4px 0px 0px #1b1c1c; }
        .btn-press { transition: transform .12s ease, box-shadow .12s ease; }
        .btn-press:hover  { transform: translate(-2px,-2px); box-shadow: 8px 8px 0 #1b1c1c; }
        .btn-press:active { transform: translate(2px, 2px);  box-shadow: 2px 2px 0 #1b1c1c; }
        .nav-link { transition: transform .12s ease; }
        .nav-link:hover { transform: translate(1px,-1px); }
        /* Custom input focus */
        .px-input {
            border: 4px solid #1b1c1c;
            outline: none;
            transition: box-shadow .15s ease, border-color .15s ease;
        }
        .px-input:focus {
            border-color: #bb0509;
            box-shadow: 4px 4px 0 #bb0509;
        }
        .image-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(92px, 1fr));
            gap: 12px;
        }
        .image-preview-grid.hidden {
            display: none;
        }
        .image-preview-card {
            border: 4px solid #1b1c1c;
            background: #fff;
            box-shadow: 4px 4px 0 #1b1c1c;
            padding: 6px;
        }
        .image-preview-card img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: contain;
            background: #dbff5c;
            border: 2px solid #cac6bc;
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

<main class="max-w-[700px] mx-auto px-4 md:px-6 py-12">

    <!-- Page header -->
    <div class="mb-8">
        <div class="inline-block bg-[#dbff5c] text-[#3d4c00] text-[9px] font-bold uppercase tracking-[.12em] px-3 py-1.5 border-2 border-[#1b1c1c] shadow-[2px_2px_0_#1b1c1c] mb-4 -rotate-1">
            ADMIN PANEL
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold uppercase">Thêm Game Mới</h1>
        <p class="text-[#49473f] mt-2">Bổ sung tựa game mới vào kho retro của Pixel Vault.</p>
    </div>

    <!-- Form card -->
    <div class="bg-[#fbf9f8] border-4 border-[#1b1c1c] shadow-brutal">

        <!-- Card header stripe -->
        <div class="bg-[#1b1c1c] px-8 py-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-[#dbff5c]" style="font-variation-settings:'FILL' 1">sports_esports</span>
            <span class="text-white text-[11px] font-bold uppercase tracking-[.12em]">NHẬP THÔNG TIN GAME</span>
        </div>

        <div class="p-8 space-y-8">

            <!-- Error messages -->
            <?php if (!empty($errors)): ?>
            <div class="bg-[#ffdad6] border-4 border-[#bb0509] p-5 shadow-[4px_4px_0_#bb0509]">
                <p class="text-[11px] font-bold uppercase tracking-[.12em] text-[#bb0509] mb-3">⚠ LỖI NHẬP LIỆU</p>
                <?php foreach ($errors as $error): ?>
                    <p class="text-sm text-[#93000a] font-medium">× <?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-7">

                <!-- Game name -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-[.12em] text-[#1b1c1c]">
                        Tên Game <span class="text-[#bb0509]">*</span>
                    </label>
                    <input type="text" name="name"
                           value="<?= htmlspecialchars($name ?? '') ?>"
                           placeholder="VD: Space Invader DX, Super Mario Bros..."
                           class="px-input w-full px-5 py-4 text-base bg-white"
                           required>
                    <p class="text-[11px] text-[#49473f]">Tên đầy đủ của tựa game, bao gồm phiên bản nếu có.</p>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-[.12em] text-[#1b1c1c]">
                        Mô Tả
                    </label>
                    <textarea name="description" rows="5"
                              placeholder="Mô tả về game, tình trạng băng, nội dung chơi..."
                              class="px-input w-full px-5 py-4 text-base bg-white resize-none"><?= htmlspecialchars($description ?? '') ?></textarea>
                    <p class="text-[11px] text-[#49473f]">Tình trạng, thể loại, số người chơi, hoặc thông tin đặc biệt.</p>
                </div>

                <!-- Price -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-[.12em] text-[#1b1c1c]">
                        Giá Bán (VNĐ) <span class="text-[#bb0509]">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="price"
                               value="<?= htmlspecialchars($price ?? '') ?>"
                               placeholder="150000"
                               min="1000" step="1000"
                               class="px-input w-full px-5 py-4 text-base bg-white pr-16"
                               required>
                        <div class="absolute right-0 top-0 bottom-0 flex items-center bg-[#e4e2e1] border-l-4 border-[#1b1c1c] px-4">
                            <span class="text-[11px] font-bold text-[#49473f]">VNĐ</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-[#49473f]">Nhập số nguyên, tối thiểu 1.000 VNĐ.</p>
                </div>

                <?php include __DIR__ . '/_system_fields.php'; ?>

                <!-- Images -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-[.12em] text-[#1b1c1c]">
                        Ảnh Sản Phẩm
                    </label>
                    <div class="grid grid-cols-1 gap-3">
                        <?php for ($i = 0; $i < 3; $i++): ?>
                        <div class="border-4 border-[#1b1c1c] bg-white shadow-brutal-sm p-3 space-y-3">
                            <label class="block">
                                <span class="mb-1 block text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f]">
                                    Ảnh <?= $i + 1 ?><?= $i === 0 ? ' · ảnh chính' : '' ?>
                                </span>
                                <input type="text"
                                       name="image_urls[<?= $i ?>]"
                                       value="<?= htmlspecialchars($_POST['image_urls'][$i] ?? '') ?>"
                                       placeholder="Đường dẫn sau upload hoặc URL ngoài"
                                       class="px-input w-full bg-white px-4 py-3 text-sm font-semibold">
                            </label>
                            <label class="block">
                                <span class="mb-1 block text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f]">Chọn file cho ảnh <?= $i + 1 ?></span>
                                <input type="file"
                                       name="image_files[<?= $i ?>]"
                                       accept="image/jpeg,image/png,image/gif,image/webp"
                                       class="w-full text-sm text-[#49473f] file:mr-4 file:border-2 file:border-[#1b1c1c] file:bg-[#e4e2e1] file:px-4 file:py-2 file:text-[11px] file:font-bold file:uppercase file:tracking-[.12em] file:text-[#1b1c1c] hover:file:bg-[#dbff5c]">
                            </label>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <p class="text-[11px] text-[#49473f] leading-relaxed">Có 3 vị trí ảnh cố định. Nếu vừa nhập đường dẫn vừa chọn file ở cùng một ảnh, file upload sẽ được ưu tiên.</p>
                </div>

                <!-- Divider -->
                <div class="border-t-2 border-dashed border-[#cac6bc]"></div>

                <!-- Actions -->
                <div class="flex gap-4">
                    <a href="<?= url() ?>"
                       class="flex-1 text-center py-4 border-4 border-[#1b1c1c] text-[11px] font-bold uppercase tracking-[.12em] hover:bg-[#e4e2e1] transition-all shadow-brutal-sm">
                        HỦY
                    </a>
                    <button type="submit"
                            class="flex-1 btn-press bg-[#bb0509] text-white py-4 border-4 border-[#1b1c1c] shadow-brutal text-[11px] font-bold uppercase tracking-[.12em] flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:18px">save</span>
                        LƯU GAME
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Tip box -->
    <div class="mt-6 bg-[#dbff5c] border-4 border-[#1b1c1c] shadow-brutal-sm p-5 flex gap-4 items-start">
        <span class="material-symbols-outlined text-[#3d4c00] shrink-0" style="font-variation-settings:'FILL' 1">lightbulb</span>
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[.12em] text-[#3d4c00] mb-1">MẸO NHỎ</p>
            <p class="text-sm text-[#3d4c00]">Mô tả rõ tình trạng băng (còn hộp, có sticker, đã test...) để người mua an tâm hơn khi đặt hàng.</p>
        </div>
    </div>

</main>

<!-- ─── FOOTER ─── -->
<footer class="bg-[#e4e2e1] border-t-4 border-[#1b1c1c] mt-24">
    <div class="max-w-[1200px] mx-auto px-4 md:px-12 py-10 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="font-brand text-lg font-bold italic text-[#1b1c1c]">PIXEL_VAULT</div>
        <div class="flex flex-wrap justify-center gap-6 text-[10px] font-bold uppercase tracking-[.12em]">
            <a href="<?= url() ?>#policy" class="text-[#49473f] hover:text-[#526600] underline transition-colors">Chính Sách</a>
            <a href="<?= url() ?>#support" class="text-[#49473f] hover:text-[#526600] underline transition-colors">Hỗ Trợ</a>
            <a href="<?= url('Admin') ?>" class="text-[#49473f] hover:text-[#526600] underline transition-colors">Quản Trị</a>
        </div>
        <div class="text-[10px] text-[#49473f] font-medium">© 1989–2026 PIXEL VAULT. MỌI QUYỀN ĐƯỢC BẢO LƯU.</div>
    </div>
</footer>

</body>
</html>

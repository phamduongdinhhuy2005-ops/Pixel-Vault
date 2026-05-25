<footer class="mt-24 border-t-4 border-[#bb0509] bg-[#1b1c1c]">
    <div class="mx-auto grid max-w-[1200px] grid-cols-1 gap-10 px-4 py-12 md:grid-cols-3 md:px-12">
        <div class="space-y-4">
            <div class="font-brand text-2xl font-bold italic text-[#bb0509]">PIXEL_VAULT</div>
            <p class="max-w-xs text-sm leading-relaxed text-zinc-400">Cửa hàng game retro chính hãng. Đưa ký ức tuổi thơ trở lại với những băng game cổ điển 8-bit.</p>
            <div class="flex gap-3 pt-2">
                <?php foreach ([['videogame_asset','FB'],['photo_camera','IG'],['chat_bubble','ZALO']] as [$icon, $label]): ?>
                    <span class="inline-flex items-center gap-1.5 border-2 border-zinc-600 bg-zinc-800 px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-zinc-400 transition-all hover:border-[#dbff5c] hover:text-[#dbff5c]">
                        <span class="material-symbols-outlined" style="font-size:14px"><?= $icon ?></span><?= $label ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="space-y-4">
            <p class="border-b border-zinc-700 pb-3 text-[11px] font-bold uppercase tracking-[.15em] text-zinc-500">Điều Hướng</p>
            <div class="space-y-3">
                <a href="<?= url() ?>" class="flex items-center gap-2 text-sm text-zinc-400 transition-colors hover:text-white">
                    <span class="h-1.5 w-1.5 shrink-0 bg-[#bb0509]"></span>Cửa Hàng
                </a>
                <a href="<?= url() ?>#about" class="flex items-center gap-2 text-sm text-zinc-400 transition-colors hover:text-white">
                    <span class="h-1.5 w-1.5 shrink-0 bg-[#bb0509]"></span>Giới Thiệu
                </a>
                <a href="<?= url() ?>#leaderboard" class="flex items-center gap-2 text-sm text-zinc-400 transition-colors hover:text-white">
                    <span class="h-1.5 w-1.5 shrink-0 bg-[#bb0509]"></span>Bảng Điểm
                </a>
                <a href="<?= url('Admin') ?>" class="flex items-center gap-2 text-sm text-zinc-400 transition-colors hover:text-white">
                    <span class="h-1.5 w-1.5 shrink-0 bg-[#bb0509]"></span>Quản Trị
                </a>
            </div>
        </div>

        <div class="space-y-4">
            <p class="border-b border-zinc-700 pb-3 text-[11px] font-bold uppercase tracking-[.15em] text-zinc-500">Hỗ Trợ & Pháp Lý</p>
            <div class="space-y-3">
                <a href="<?= url() ?>#policy" class="flex items-center gap-2 text-sm text-zinc-400 transition-colors hover:text-white">
                    <span class="h-1.5 w-1.5 shrink-0 bg-[#526600]"></span>Chính Sách
                </a>
                <a href="<?= url() ?>#terms" class="flex items-center gap-2 text-sm text-zinc-400 transition-colors hover:text-white">
                    <span class="h-1.5 w-1.5 shrink-0 bg-[#526600]"></span>Điều Khoản
                </a>
                <a href="<?= url() ?>#support" class="flex items-center gap-2 text-sm text-zinc-400 transition-colors hover:text-white">
                    <span class="h-1.5 w-1.5 shrink-0 bg-[#526600]"></span>Hỗ Trợ
                </a>
            </div>
            <div class="mt-4 space-y-2 border-2 border-zinc-600 bg-zinc-800 p-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Liên Hệ Nhanh</p>
                <p class="font-brand text-sm font-bold text-[#dbff5c]">0901 234 567</p>
                <p class="text-xs text-zinc-500">T2-T7 · 9:00-21:00</p>
            </div>
        </div>
    </div>

    <div class="border-t border-zinc-800">
        <div class="mx-auto flex max-w-[1200px] flex-col items-center justify-between gap-3 px-4 py-5 md:flex-row md:px-12">
            <div class="font-brand text-[10px] uppercase tracking-[.15em] text-zinc-600">© 1989-2026 PIXEL VAULT. Mọi quyền được bảo lưu.</div>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                <span class="font-brand text-[9px] uppercase tracking-widest text-zinc-600">Hệ thống đang hoạt động</span>
            </div>
        </div>
    </div>
</footer>

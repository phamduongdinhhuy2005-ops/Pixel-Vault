<?php
$sharedCartQuantity = array_sum(array_map(
    fn($item) => (int) ($item['quantity'] ?? 0),
    $_SESSION['cart'] ?? []
));
$activePage = $activePage ?? '';
$navClass = fn(string $page): string => $activePage === $page
    ? 'text-[#bb0509] underline decoration-4 underline-offset-4'
    : 'text-[#1b1c1c] hover:text-[#bb0509]';
$tickerText = '★ PIXEL VAULT - RETRO GAMING STORE · HÀNG CỔ ĐIỂN CHÍNH HÃNG · BĂNG GAME 8-BIT · MÁY CHƠI GAME RETRO · VẬN CHUYỂN TOÀN QUỐC · CHẤT LƯỢNG TỪ NĂM 1989';
?>
<style>
    html {
        font-size: 88%;
    }
    @media (max-width: 640px) {
        html {
            font-size: 92%;
        }
    }

    @keyframes pv-shared-ticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    .pv-shared-ticker-track { width: max-content; animation: pv-shared-ticker 34s linear infinite; }
    .pv-shared-mobile-menu { display: none; }
    .pv-shared-mobile-menu.open { display: block; }

    /* Active nav underline animation */
    @keyframes pv-underline-in {
        from { transform: scaleX(0); opacity: 0; }
        to   { transform: scaleX(1); opacity: 1; }
    }
    .pv-nav-link {
        position: relative;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
    }
    .pv-nav-link::after {
        content: '';
        display: block;
        height: 3px;
        width: 100%;
        background-color: #bb0509;
        border-radius: 1px;
        transform: scaleX(0);
        transform-origin: left center;
        transition: transform .22s cubic-bezier(.16,1,.3,1);
    }
    .pv-nav-link:hover::after {
        transform: scaleX(1);
    }
    .pv-nav-link.active::after {
        transform: scaleX(1);
        animation: pv-underline-in .28s cubic-bezier(.16,1,.3,1) both;
    }
</style>
<nav class="sticky top-0 z-50 border-b-4 border-[#1b1c1c] bg-[#fbf9f8] shadow-[0px_4px_0px_0px_#1b1c1c]">
    <div class="overflow-hidden bg-[#1b1c1c] py-1.5 text-[#dbff5c]">
        <div class="pv-shared-ticker-track flex gap-8 whitespace-nowrap font-brand text-[9px] font-bold uppercase tracking-[.15em]" aria-hidden="true">
            <?php for ($i = 0; $i < 4; $i++): ?>
                <span><?= htmlspecialchars($tickerText) ?></span>
            <?php endfor; ?>
        </div>
    </div>

    <div class="mx-auto flex max-w-[1200px] items-center justify-between px-4 py-4 md:px-12">
        <div class="flex items-center gap-4">
            <a href="<?= url() ?>" class="font-brand text-xl font-bold italic tracking-tighter text-[#bb0509] sm:text-2xl">PIXEL_VAULT</a>
            <span class="hidden border-l-2 border-[#cac6bc] pl-4 text-[10px] font-medium leading-tight text-[#49473f] md:block">
                Retro Gaming<br>since 1989
            </span>
        </div>

        <div class="hidden items-center gap-8 md:flex">
            <a href="<?= url() ?>" class="pv-nav-link font-brand text-[11px] font-bold uppercase tracking-[.12em] <?= $activePage === 'home' ? 'text-[#bb0509] active' : 'text-[#1b1c1c] hover:text-[#bb0509]' ?>">Cửa Hàng</a>
            <a href="<?= url() ?>#leaderboard" class="pv-nav-link font-brand text-[11px] font-bold uppercase tracking-[.12em] <?= $activePage === 'leaderboard' ? 'text-[#bb0509] active' : 'text-[#1b1c1c] hover:text-[#bb0509]' ?>">Bảng Điểm</a>
            <a href="<?= url() ?>#about" class="pv-nav-link font-brand text-[11px] font-bold uppercase tracking-[.12em] <?= $activePage === 'about' ? 'text-[#bb0509] active' : 'text-[#1b1c1c] hover:text-[#bb0509]' ?>">Giới Thiệu</a>
            <a href="<?= url('Admin') ?>" class="pv-nav-link font-brand text-[11px] font-bold uppercase tracking-[.12em] <?= $activePage === 'admin' ? 'text-[#bb0509] active' : 'text-[#1b1c1c] hover:text-[#bb0509]' ?>">Quản Trị</a>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="toggleSharedMobileMenu()" class="flex h-10 w-10 items-center justify-center border-2 border-[#1b1c1c] bg-white text-[#1b1c1c] md:hidden" aria-label="Mở menu" aria-expanded="false" id="shared-mobile-menu-button">
                <span class="material-symbols-outlined" style="font-size:22px">menu</span>
            </button>
            <a href="<?= url('Product/cart') ?>" class="relative text-[#615e57] transition-colors hover:text-[#bb0509] <?= $activePage === 'cart' ? 'text-[#bb0509]' : '' ?>" aria-label="Mở giỏ hàng">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:28px">shopping_cart</span>
                <?php if ($sharedCartQuantity > 0): ?>
                    <span class="absolute -right-2 -top-2 flex h-5 min-w-5 items-center justify-center rounded-full border-2 border-[#1b1c1c] bg-[#bb0509] px-1 text-[9px] font-bold text-white"><?= $sharedCartQuantity ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <div id="shared-mobile-menu" class="pv-shared-mobile-menu bg-[#fbf9f8] md:hidden">
        <div class="grid grid-cols-2 gap-3 px-4 py-4">
            <a href="<?= url() ?>" class="border-2 border-[#1b1c1c] bg-white px-3 py-3 text-center text-[10px] font-bold uppercase tracking-[.12em] text-[#bb0509]">Cửa hàng</a>
            <a href="<?= url() ?>#leaderboard" class="border-2 border-[#1b1c1c] bg-white px-3 py-3 text-center text-[10px] font-bold uppercase tracking-[.12em]">Bảng điểm</a>
            <a href="<?= url() ?>#about" class="border-2 border-[#1b1c1c] bg-white px-3 py-3 text-center text-[10px] font-bold uppercase tracking-[.12em]">Giới thiệu</a>
            <a href="<?= url('Admin') ?>" class="border-2 border-[#1b1c1c] bg-white px-3 py-3 text-center text-[10px] font-bold uppercase tracking-[.12em]">Quản trị</a>
        </div>
    </div>
</nav>
<script>
function toggleSharedMobileMenu() {
    const menu = document.getElementById('shared-mobile-menu');
    const button = document.getElementById('shared-mobile-menu-button');
    if (!menu) return;
    const isOpen = menu.classList.toggle('open');
    if (button) button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}
</script>

<?php
$systemOptions = $systemOptions ?? [];
$systemInfo = $systemInfo ?? [
    'category' => 'Nội địa',
    'condition' => 'Đã qua sử dụng',
    'resolution' => '256 × 224',
    'rom_format' => '16MB ROM',
    'players' => '1 người chơi',
    'region' => 'Tự do',
    'genres' => [],
];
$selectedGenres = $systemInfo['genres'] ?? [];
if (!is_array($selectedGenres)) {
    $selectedGenres = $selectedGenres === '' ? [] : [$selectedGenres];
}
$systemFieldLabels = [
    'category' => ['Loại băng', 'category'],
    'condition' => ['Tình trạng', 'inventory_2'],
    'resolution' => ['Độ phân giải', 'aspect_ratio'],
    'rom_format' => ['Định dạng', 'memory'],
    'players' => ['Số người chơi', 'group'],
];
?>
<div class="space-y-4">
    <div class="flex items-center gap-3 border-b-2 border-dashed border-[#cac6bc] pb-3">
        <span class="material-symbols-outlined text-[#bb0509]" style="font-variation-settings:'FILL' 1">settings_input_component</span>
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[.12em] text-[#1b1c1c]">Thông tin hệ thống</p>
            <p class="text-[11px] text-[#49473f]">Chọn thông số có sẵn; thể loại có thể chọn nhiều mục.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php foreach ($systemFieldLabels as $field => [$label, $icon]): ?>
        <label class="block">
            <span class="mb-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f]">
                <span class="material-symbols-outlined text-[#526600]" style="font-size:16px"><?= $icon ?></span>
                <?= $label ?>
            </span>
            <select name="<?= $field ?>" class="px-input w-full bg-white px-4 py-3 text-sm font-semibold">
                <?php foreach (($systemOptions[$field] ?? []) as $option): ?>
                <option value="<?= htmlspecialchars($option) ?>" <?= ($systemInfo[$field] ?? '') === $option ? 'selected' : '' ?>>
                    <?= htmlspecialchars($option) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </label>
        <?php endforeach; ?>
    </div>

    <label class="block">
        <span class="mb-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f]">
            <span class="material-symbols-outlined text-[#526600]" style="font-size:16px">public</span>
            Khu vực
        </span>
        <input type="text" name="region"
               value="<?= htmlspecialchars($systemInfo['region'] ?? 'Tự do') ?>"
               placeholder="VD: Nhật Bản, US, EU, Tự do..."
               class="px-input w-full bg-white px-4 py-3 text-sm font-semibold">
    </label>

    <fieldset class="block">
        <legend class="mb-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[.12em] text-[#49473f]">
            <span class="material-symbols-outlined text-[#526600]" style="font-size:16px">category</span>
            Thể loại
        </legend>
        <div class="px-input grid grid-cols-1 sm:grid-cols-2 gap-2 bg-white p-4" data-genre-limit="4">
            <?php foreach (($systemOptions['genres'] ?? []) as $genre): ?>
            <label class="flex items-center gap-2 border-2 border-[#e4e2e1] px-3 py-2 text-sm font-semibold hover:border-[#bb0509] hover:bg-[#fff7f7] transition-colors">
                <input type="checkbox"
                       name="genres[]"
                       value="<?= htmlspecialchars($genre) ?>"
                       <?= in_array($genre, $selectedGenres, true) ? 'checked' : '' ?>
                       data-genre-checkbox
                       class="border-2 border-[#1b1c1c] text-[#bb0509] focus:ring-0">
                <span><?= htmlspecialchars($genre) ?></span>
            </label>
            <?php endforeach; ?>
        </div>
        <p class="mt-2 text-[11px] text-[#49473f]">Một game có thể thuộc tối đa 4 thể loại.</p>
    </fieldset>
</div>

<script>
document.querySelectorAll('[data-genre-limit]').forEach(group => {
    const limit = Number(group.dataset.genreLimit || 4);
    const checkboxes = Array.from(group.querySelectorAll('[data-genre-checkbox]'));

    const syncGenreLimit = () => {
        const checkedCount = checkboxes.filter(input => input.checked).length;
        checkboxes.forEach(input => {
            input.disabled = !input.checked && checkedCount >= limit;
            input.closest('label')?.classList.toggle('opacity-45', input.disabled);
        });
    };

    checkboxes.forEach(input => input.addEventListener('change', syncGenreLimit));
    syncGenreLimit();
});
</script>

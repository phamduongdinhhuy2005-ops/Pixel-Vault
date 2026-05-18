<?php

class ProductModel {
    private int $ID;
    private string $Name;
    private string $Description;
    private float $Price;
    private array $Images;
    private array $SystemInfo;

    public function __construct(int $ID, string $Name, string $Description, float $Price, array $Images = [], array $SystemInfo = []) {
        $this->ID = $ID;
        $this->Name = $Name;
        $this->Description = $Description;
        $this->Price = $Price;
        $this->Images = $this->normalizeImageSlots($Images);
        $this->SystemInfo = $this->normalizeSystemInfo($SystemInfo);
    }

    public function getID(): int { return $this->ID; }
    public function getName(): string { return $this->Name; }
    public function getDescription(): string { return $this->Description; }
    public function getPrice(): float { return $this->Price; }
    public function getImages(): array {
        return array_values(array_filter($this->Images, fn(string $image) => $image !== ''));
    }
    public function getImageSlots(): array {
        return $this->Images;
    }
    public function getPrimaryImage(): ?string {
        $images = array_values(array_filter($this->getImageSlots(), fn(string $image) => $image !== ''));
        return $images[0] ?? null;
    }
    public function getSystemInfo(): array {
        return $this->SystemInfo;
    }
    public function getCondition(): string { return $this->getSystemInfo()['condition']; }
    public function getResolution(): string { return $this->getSystemInfo()['resolution']; }
    public function getRomFormat(): string { return $this->getSystemInfo()['rom_format']; }
    public function getPlayers(): string { return $this->getSystemInfo()['players']; }
    public function getRegion(): string { return $this->getSystemInfo()['region']; }
    public function getCategory(): string { return $this->getSystemInfo()['category']; }
    public function getGenres(): array { return $this->getSystemInfo()['genres']; }
    public function getGenreText(): string { return implode(', ', $this->getGenres()); }

    private function normalizeImageSlots(array $Images): array {
        $slots = [];
        for ($index = 0; $index < 3; $index++) {
            $slots[$index] = trim((string) ($Images[$index] ?? ''));
        }

        return $slots;
    }

    private function normalizeSystemInfo(array $SystemInfo): array {
        $genres = $SystemInfo['genres'] ?? [];
        if (!is_array($genres)) {
            $genres = $genres === '' ? [] : [$genres];
        }

        $genres = array_values(array_unique(array_filter(array_map(
            fn($genre) => trim((string) $genre),
            $genres
        ))));

        return [
            'condition' => trim((string) ($SystemInfo['condition'] ?? 'Đã qua sử dụng')),
            'resolution' => trim((string) ($SystemInfo['resolution'] ?? '256 × 224')),
            'rom_format' => trim((string) ($SystemInfo['rom_format'] ?? '16MB ROM')),
            'players' => trim((string) ($SystemInfo['players'] ?? '1 người chơi')),
            'region' => trim((string) ($SystemInfo['region'] ?? 'Tự do')),
            'category' => trim((string) ($SystemInfo['category'] ?? 'Nội địa')),
            'genres' => $genres,
        ];
    }
}
?>

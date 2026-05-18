<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';

class ProductController {
    private const IMAGE_UPLOAD_DIR = __DIR__ . '/../../uploads/products';
    private const MAX_IMAGE_SIZE = 4194304;
    private const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const IMAGE_SLOT_COUNT = 3;
    private const MAX_GENRES_PER_PRODUCT = 4;
    private const PRODUCTS_PER_PAGE = 4;
    private const FALLBACK_SYSTEM_OPTIONS = [
        'category' => ['Nhập khẩu', 'Nội địa', 'Đặc biệt'],
        'condition' => ['Mới', 'Đã qua sử dụng'],
        'resolution' => ['160 × 144', '256 × 224', '256 × 240', '320 × 224', '320 × 240', '426 × 240', '480 × 270'],
        'rom_format' => ['8MB ROM', '16MB ROM', '24MB ROM'],
        'players' => ['1 người chơi', '2 người chơi'],
        'genres' => ['Action', 'Adventure', 'RPG', 'Metroidvania', 'Horror', 'Visual Novel', 'Platformer', 'Puzzle', 'Racing', 'Fighting', 'Strategy', 'Simulation'],
    ];

    private ?PDO $db;
    private array $systemOptions = self::FALLBACK_SYSTEM_OPTIONS;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();

        if ($this->db === null) {
            http_response_code(500);
            die('Không thể kết nối cơ sở dữ liệu pixel_vault.');
        }

        $this->systemOptions = $this->loadSystemOptions();
    }

    public function list(): void {
        $systemOptions = $this->systemOptions;
        $filters = $this->readProductFilters($_GET);
        $allProducts = $this->fetchProducts();
        $filteredProducts = $this->filterProducts($allProducts, $filters);
        $totalFilteredProducts = count($filteredProducts);
        $perPage = self::PRODUCTS_PER_PAGE;
        $totalPages = max(1, (int) ceil($totalFilteredProducts / $perPage));
        $currentPage = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));
        $products = array_slice($filteredProducts, ($currentPage - 1) * $perPage, $perPage);
        $totalProducts = count($allProducts);
        include __DIR__ . '/../views/product/list.php';
    }

    public function detail(int $id): void {
        $product = $this->findProduct($id);

        if ($product === null) {
            $this->notFound();
        }

        include __DIR__ . '/../views/product/detail.php';
    }

    public function add(): void {
        $errors = [];
        $systemOptions = $this->systemOptions;
        $name = '';
        $description = '';
        $price = '';
        $systemInfo = $this->defaultSystemInfo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$name, $description, $price, $systemInfo, $errors] = $this->validateProductInput($_POST);
            $imageSlots = $this->readImageSlots($_POST);
            $slotUploads = $this->handleImageSlotUploads($_FILES['image_files'] ?? [], $errors);
            $finalImages = $this->mergeImageSlots($imageSlots, $slotUploads);

            if (empty($errors)) {
                $newId = $this->createProduct($name, $description, (float) $price, $finalImages, $systemInfo);
                $_SESSION['flash_success'] = 'Đã thêm sản phẩm vào MySQL. Bạn có thể tiếp tục chỉnh sửa thông tin.';
                $this->redirect(url('Product/edit/' . $newId));
            }

            if (!empty($slotUploads)) {
                $this->deleteImageFiles($slotUploads);
            }
        }

        include __DIR__ . '/../views/product/add.php';
    }

    public function edit(int $id): void {
        $product = $this->findProduct($id);

        if ($product === null) {
            $this->notFound();
        }

        $errors = [];
        $systemOptions = $this->systemOptions;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$name, $description, $price, $systemInfo, $errors] = $this->validateProductInput($_POST);
            $imageSlots = $this->readImageSlots($_POST);
            $slotUploads = $this->handleImageSlotUploads($_FILES['image_files'] ?? [], $errors);
            $finalImages = $this->mergeImageSlots($imageSlots, $slotUploads);

            if (empty($errors)) {
            $currentImages = $product->getImages();
            $unusedImages = array_values(array_diff($currentImages, array_filter($finalImages)));

                $this->updateProduct($id, $name, $description, (float) $price, $finalImages, $systemInfo);
                $this->deleteImageFiles($unusedImages);
                $_SESSION['flash_success'] = 'Đã lưu thay đổi sản phẩm vào MySQL.';
                $this->redirect(url('Product/edit/' . $id));
            }

            if (!empty($slotUploads)) {
                $this->deleteImageFiles($slotUploads);
            }
        }

        include __DIR__ . '/../views/product/edit.php';
    }

    public function delete(int $id): void {
        $product = $this->findProduct($id);

        if ($product !== null) {
            $this->deleteProduct($id);
            $this->deleteImageFiles($product->getImages());
        }

        $this->redirect(url('Product/list'));
    }

    public function admin(): void {
        $products = $this->fetchProducts();
        $categories = $this->fetchCategories();
        $systemOptions = $this->systemOptions;
        include __DIR__ . '/../views/admin/index.php';
    }

    public function categoryAdd(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url('Admin'));
        }

        [$name, $description, $errors] = $this->validateCategoryInput($_POST);

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            $this->redirect(url('Admin') . '#categories');
        }

        $this->createCategory($name, $description);
        $_SESSION['flash_success'] = 'Đã thêm danh mục mới.';
        $this->redirect(url('Admin') . '#categories');
    }

    public function categoryEdit(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url('Admin'));
        }

        [$name, $description, $errors] = $this->validateCategoryInput($_POST, $id);

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            $this->redirect(url('Admin') . '#categories');
        }

        $this->updateCategory($id, $name, $description);
        $_SESSION['flash_success'] = 'Đã cập nhật danh mục.';
        $this->redirect(url('Admin') . '#categories');
    }

    public function categoryDelete(int $id): void {
        $this->deleteCategory($id);
        $_SESSION['flash_success'] = 'Đã xóa danh mục. Sản phẩm thuộc danh mục này sẽ chuyển sang trạng thái chưa phân loại.';
        $this->redirect(url('Admin') . '#categories');
    }

    private function loadSystemOptions(): array {
        $options = self::FALLBACK_SYSTEM_OPTIONS;
        $options['category'] = $this->fetchColumnValues('categories', 'name') ?: $options['category'];
        $options['genres'] = $this->fetchColumnValues('genres', 'name') ?: $options['genres'];

        return $options;
    }

    private function fetchColumnValues(string $table, string $column): array {
        $allowed = [
            'categories' => ['name'],
            'genres' => ['name'],
        ];

        if (!isset($allowed[$table]) || !in_array($column, $allowed[$table], true)) {
            return [];
        }

        $stmt = $this->db->query("SELECT {$column} FROM {$table} ORDER BY id");
        return array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function fetchProducts(): array {
        $stmt = $this->db->query(
            'SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             ORDER BY p.id'
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $products = [];

        foreach ($rows as $row) {
            $id = (int) $row['id'];
            $products[] = $this->hydrateProduct(
                $row,
                $this->fetchProductImages($id),
                $this->fetchProductGenres($id)
            );
        }

        return $products;
    }

    private function fetchCategories(): array {
        $stmt = $this->db->query(
            'SELECT c.id, c.name, c.description, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             GROUP BY c.id, c.name, c.description
             ORDER BY c.id'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function findProduct(int $id): ?ProductModel {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->hydrateProduct($row, $this->fetchProductImages($id), $this->fetchProductGenres($id));
    }

    private function hydrateProduct(array $row, array $images, array $genres): ProductModel {
        return new ProductModel(
            (int) $row['id'],
            (string) $row['name'],
            (string) ($row['description'] ?? ''),
            (float) $row['price'],
            array_map(fn(string $image) => $this->imageForDisplay($image), $images),
            [
                'category' => (string) ($row['category_name'] ?? 'Chưa phân loại'),
                'condition' => (string) ($row['product_condition'] ?? 'Đã qua sử dụng'),
                'resolution' => (string) ($row['resolution'] ?? '256 × 224'),
                'rom_format' => (string) ($row['rom_format'] ?? '16MB ROM'),
                'players' => (string) ($row['players'] ?? '1 người chơi'),
                'region' => (string) ($row['region'] ?? 'Tự do'),
                'genres' => $genres,
            ]
        );
    }

    private function fetchProductImages(int $productId): array {
        $stmt = $this->db->prepare(
            'SELECT image_slot, image_url
             FROM product_images
             WHERE product_id = :product_id
             ORDER BY image_slot'
        );
        $stmt->execute(['product_id' => $productId]);

        $images = array_fill(0, self::IMAGE_SLOT_COUNT, '');
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $index = max(0, min(self::IMAGE_SLOT_COUNT - 1, (int) $row['image_slot'] - 1));
            $images[$index] = (string) $row['image_url'];
        }

        return $images;
    }

    private function fetchProductGenres(int $productId): array {
        $stmt = $this->db->prepare(
            'SELECT g.name
             FROM product_genres pg
             INNER JOIN genres g ON pg.genre_id = g.id
             WHERE pg.product_id = :product_id
             ORDER BY g.name'
        );
        $stmt->execute(['product_id' => $productId]);

        return array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function createProduct(string $name, string $description, float $price, array $images, array $systemInfo): int {
        $this->db->beginTransaction();

        try {
            $productId = $this->nextAvailableId('products');
            $stmt = $this->db->prepare(
                'INSERT INTO products (id, name, description, price, category_id, product_condition, resolution, rom_format, players, region)
                 VALUES (:id, :name, :description, :price, :category_id, :product_condition, :resolution, :rom_format, :players, :region)'
            );
            $stmt->execute([
                'id' => $productId,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $this->categoryIdByName($systemInfo['category']),
                'product_condition' => $systemInfo['condition'],
                'resolution' => $systemInfo['resolution'],
                'rom_format' => $systemInfo['rom_format'],
                'players' => $systemInfo['players'],
                'region' => $systemInfo['region'],
            ]);

            $this->replaceProductImages($productId, $images);
            $this->replaceProductGenres($productId, $systemInfo['genres']);
            $this->db->commit();
            $this->resetAutoIncrement('products');

            return $productId;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    private function updateProduct(int $id, string $name, string $description, float $price, array $images, array $systemInfo): void {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'UPDATE products
                 SET name = :name,
                     description = :description,
                     price = :price,
                     category_id = :category_id,
                     product_condition = :product_condition,
                     resolution = :resolution,
                     rom_format = :rom_format,
                     players = :players,
                     region = :region
                 WHERE id = :id'
            );
            $stmt->execute([
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $this->categoryIdByName($systemInfo['category']),
                'product_condition' => $systemInfo['condition'],
                'resolution' => $systemInfo['resolution'],
                'rom_format' => $systemInfo['rom_format'],
                'players' => $systemInfo['players'],
                'region' => $systemInfo['region'],
            ]);

            $this->replaceProductImages($id, $images);
            $this->replaceProductGenres($id, $systemInfo['genres']);
            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    private function deleteProduct(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $this->resetAutoIncrement('products');
    }

    private function replaceProductImages(int $productId, array $images): void {
        $stmt = $this->db->prepare('DELETE FROM product_images WHERE product_id = :product_id');
        $stmt->execute(['product_id' => $productId]);

        $stmt = $this->db->prepare(
            'INSERT INTO product_images (id, product_id, image_url, image_slot, is_primary)
             VALUES (:id, :product_id, :image_url, :image_slot, :is_primary)'
        );

        $images = array_slice(array_pad($images, self::IMAGE_SLOT_COUNT, ''), 0, self::IMAGE_SLOT_COUNT);

        foreach ($images as $index => $image) {
            if ($image === '') {
                continue;
            }

            $stmt->execute([
                'id' => $this->nextAvailableId('product_images'),
                'product_id' => $productId,
                'image_url' => $this->imageForDatabase($image),
                'image_slot' => $index + 1,
                'is_primary' => $index === 0 ? 1 : 0,
            ]);
        }

    }

    private function replaceProductGenres(int $productId, array $genres): void {
        $stmt = $this->db->prepare('DELETE FROM product_genres WHERE product_id = :product_id');
        $stmt->execute(['product_id' => $productId]);

        $stmt = $this->db->prepare(
            'INSERT INTO product_genres (product_id, genre_id)
             VALUES (:product_id, :genre_id)'
        );

        foreach ($genres as $genre) {
            $genreId = $this->genreIdByName($genre);
            if ($genreId === null) {
                continue;
            }

            $stmt->execute([
                'product_id' => $productId,
                'genre_id' => $genreId,
            ]);
        }
    }

    private function categoryIdByName(string $name): ?int {
        $stmt = $this->db->prepare('SELECT id FROM categories WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => $name]);
        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    private function createCategory(string $name, string $description): void {
        $stmt = $this->db->prepare(
            'INSERT INTO categories (id, name, description)
             VALUES (:id, :name, :description)'
        );
        $stmt->execute([
            'id' => $this->nextAvailableId('categories'),
            'name' => $name,
            'description' => $description,
        ]);
        $this->resetAutoIncrement('categories');
    }

    private function updateCategory(int $id, string $name, string $description): void {
        $stmt = $this->db->prepare(
            'UPDATE categories
             SET name = :name, description = :description
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'description' => $description,
        ]);
    }

    private function deleteCategory(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $this->resetAutoIncrement('categories');
    }

    private function categoryNameExists(string $name, ?int $excludeId = null): bool {
        $sql = 'SELECT COUNT(*) FROM categories WHERE name = :name';
        $params = ['name' => $name];

        if ($excludeId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function genreIdByName(string $name): ?int {
        $stmt = $this->db->prepare('SELECT id FROM genres WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => $name]);
        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    private function nextAvailableId(string $table): int {
        if (!in_array($table, ['products', 'product_images', 'categories'], true)) {
            throw new InvalidArgumentException('Bảng không được phép cấp ID thủ công.');
        }

        $ids = array_map('intval', $this->db->query("SELECT id FROM {$table} ORDER BY id")->fetchAll(PDO::FETCH_COLUMN));
        $nextId = 1;

        foreach ($ids as $id) {
            if ($id === $nextId) {
                $nextId++;
                continue;
            }

            if ($id > $nextId) {
                break;
            }
        }

        return $nextId;
    }

    private function resetAutoIncrement(string $table): void {
        if (!in_array($table, ['products', 'product_images', 'categories'], true)) {
            throw new InvalidArgumentException('Bảng không được phép reset AUTO_INCREMENT.');
        }

        $nextId = ((int) $this->db->query("SELECT COALESCE(MAX(id), 0) + 1 FROM {$table}")->fetchColumn());
        $this->db->exec("ALTER TABLE {$table} AUTO_INCREMENT = {$nextId}");
    }

    private function validateCategoryInput(array $input, ?int $excludeId = null): array {
        $name = trim((string) ($input['name'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $errors = [];

        $nameLength = function_exists('mb_strlen') ? mb_strlen($name) : strlen($name);
        if ($name === '') {
            $errors[] = 'Tên danh mục là bắt buộc.';
        } elseif ($nameLength < 2 || $nameLength > 100) {
            $errors[] = 'Tên danh mục phải từ 2 đến 100 ký tự.';
        } elseif ($this->categoryNameExists($name, $excludeId)) {
            $errors[] = 'Danh mục này đã tồn tại.';
        }

        return [$name, $description, $errors];
    }

    private function validateProductInput(array $input): array {
        $name = trim((string) ($input['name'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $price = (float) ($input['price'] ?? 0);
        $systemInfo = [
            'category' => trim((string) ($input['category'] ?? '')),
            'condition' => trim((string) ($input['condition'] ?? '')),
            'resolution' => trim((string) ($input['resolution'] ?? '')),
            'rom_format' => trim((string) ($input['rom_format'] ?? '')),
            'players' => trim((string) ($input['players'] ?? '')),
            'region' => trim((string) ($input['region'] ?? '')),
            'genres' => $this->validGenres($input['genres'] ?? []),
        ];
        $errors = [];

        $nameLength = function_exists('mb_strlen') ? mb_strlen($name) : strlen($name);
        if ($name === '') {
            $errors[] = 'Tên game là bắt buộc.';
        } elseif ($nameLength < 3 || $nameLength > 100) {
            $errors[] = 'Tên game phải từ 3 đến 100 ký tự.';
        }

        if ($price < 1000) {
            $errors[] = 'Giá bán tối thiểu là 1.000 VNĐ.';
        }

        foreach (['category' => 'loại băng', 'condition' => 'tình trạng', 'resolution' => 'độ phân giải', 'rom_format' => 'định dạng', 'players' => 'số người chơi'] as $field => $label) {
            if (!in_array($systemInfo[$field], $this->systemOptions[$field], true)) {
                $errors[] = 'Vui lòng chọn ' . $label . ' hợp lệ.';
            }
        }

        if (empty($systemInfo['genres'])) {
            $errors[] = 'Vui lòng chọn ít nhất một thể loại hợp lệ.';
        } elseif (count($systemInfo['genres']) > self::MAX_GENRES_PER_PRODUCT) {
            $errors[] = 'Mỗi game chỉ được chọn tối đa ' . self::MAX_GENRES_PER_PRODUCT . ' thể loại.';
        }

        if ($systemInfo['region'] === '') {
            $systemInfo['region'] = 'Tự do';
        }

        return [$name, $description, $price, $systemInfo, $errors];
    }

    private function defaultSystemInfo(): array {
        return [
            'category' => $this->systemOptions['category'][1] ?? 'Nội địa',
            'condition' => $this->systemOptions['condition'][1],
            'resolution' => $this->systemOptions['resolution'][1],
            'rom_format' => $this->systemOptions['rom_format'][1],
            'players' => $this->systemOptions['players'][0],
            'region' => 'Tự do',
            'genres' => [$this->systemOptions['genres'][0] ?? 'Action'],
        ];
    }

    private function readProductFilters(array $input): array {
        return [
            'q' => trim((string) ($input['q'] ?? '')),
            'category' => $this->validFilterValue('category', $input['category'] ?? ''),
            'condition' => $this->validFilterValue('condition', $input['condition'] ?? ''),
            'resolution' => $this->validFilterValue('resolution', $input['resolution'] ?? ''),
            'rom_format' => $this->validFilterValue('rom_format', $input['rom_format'] ?? ''),
            'players' => $this->validFilterValue('players', $input['players'] ?? ''),
            'genre' => $this->validFilterValue('genres', $input['genre'] ?? ''),
        ];
    }

    private function validFilterValue(string $field, mixed $value): string {
        $value = trim((string) $value);
        return in_array($value, $this->systemOptions[$field] ?? [], true) ? $value : '';
    }

    private function validGenres(mixed $genres): array {
        if (!is_array($genres)) {
            $genres = $genres === '' ? [] : [$genres];
        }

        return array_values(array_unique(array_filter(
            array_map(fn($genre) => trim((string) $genre), $genres),
            fn($genre) => in_array($genre, $this->systemOptions['genres'], true)
        )));
    }

    private function readImageSlots(array $input): array {
        $slots = $input['image_urls'] ?? [];
        if (!is_array($slots)) {
            $slots = [];
        }

        $images = [];
        for ($index = 0; $index < self::IMAGE_SLOT_COUNT; $index++) {
            $images[$index] = trim((string) ($slots[$index] ?? ''));
        }

        return $images;
    }

    private function mergeImageSlots(array $imageSlots, array $slotUploads): array {
        $images = array_slice(array_pad($imageSlots, self::IMAGE_SLOT_COUNT, ''), 0, self::IMAGE_SLOT_COUNT);

        foreach ($slotUploads as $index => $uploadedImage) {
            if ($index < 0 || $index >= self::IMAGE_SLOT_COUNT) {
                continue;
            }

            $images[$index] = $uploadedImage;
        }

        return $images;
    }

    private function filterProducts(array $products, array $filters): array {
        return array_values(array_filter($products, function (ProductModel $product) use ($filters): bool {
            if ($filters['q'] !== '') {
                $haystack = $this->lowerText($product->getName() . ' ' . $product->getDescription() . ' ' . $product->getRegion() . ' ' . $product->getCategory() . ' ' . $product->getGenreText());
                $needle = $this->lowerText($filters['q']);
                if (!str_contains($haystack, $needle)) {
                    return false;
                }
            }

            return ($filters['category'] === '' || $product->getCategory() === $filters['category'])
                && ($filters['condition'] === '' || $product->getCondition() === $filters['condition'])
                && ($filters['resolution'] === '' || $product->getResolution() === $filters['resolution'])
                && ($filters['rom_format'] === '' || $product->getRomFormat() === $filters['rom_format'])
                && ($filters['players'] === '' || $product->getPlayers() === $filters['players'])
                && ($filters['genre'] === '' || in_array($filters['genre'], $product->getGenres(), true));
        }));
    }

    private function lowerText(string $text): string {
        return function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    }

    private function handleImageUploads(array $files, array &$errors): array {
        if (empty($files) || empty($files['name'])) {
            return [];
        }

        $uploads = $this->normalizeUploadedFiles($files);
        $images = [];

        if (!is_dir(self::IMAGE_UPLOAD_DIR)) {
            mkdir(self::IMAGE_UPLOAD_DIR, 0775, true);
        }

        foreach ($uploads as $file) {
            if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Không thể tải một ảnh lên. Vui lòng thử lại.';
                continue;
            }

            if ($file['size'] > self::MAX_IMAGE_SIZE) {
                $errors[] = 'Mỗi ảnh sản phẩm tối đa 4MB.';
                continue;
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS, true)) {
                $errors[] = 'Ảnh chỉ hỗ trợ JPG, PNG, GIF hoặc WEBP.';
                continue;
            }

            $fileName = 'product_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $target = rtrim(self::IMAGE_UPLOAD_DIR, '/\\') . DIRECTORY_SEPARATOR . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $target)) {
                $errors[] = 'Không thể lưu ảnh sản phẩm.';
                continue;
            }

            $images[] = url('uploads/products/' . $fileName);
        }

        return $images;
    }

    private function handleImageSlotUploads(array $files, array &$errors): array {
        if (empty($files) || empty($files['name']) || !is_array($files['name'])) {
            return [];
        }

        $images = [];

        if (!is_dir(self::IMAGE_UPLOAD_DIR)) {
            mkdir(self::IMAGE_UPLOAD_DIR, 0775, true);
        }

        for ($index = 0; $index < self::IMAGE_SLOT_COUNT; $index++) {
            $file = [
                'name' => $files['name'][$index] ?? '',
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];

            if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $uploaded = $this->storeUploadedImage($file, $errors);
            if ($uploaded !== null) {
                $images[$index] = $uploaded;
            }
        }

        return $images;
    }

    private function storeUploadedImage(array $file, array &$errors): ?string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Không thể tải một ảnh lên. Vui lòng thử lại.';
            return null;
        }

        if ($file['size'] > self::MAX_IMAGE_SIZE) {
            $errors[] = 'Mỗi ảnh sản phẩm tối đa 4MB.';
            return null;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS, true)) {
            $errors[] = 'Ảnh chỉ hỗ trợ JPG, PNG, GIF hoặc WEBP.';
            return null;
        }

        $fileName = 'product_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $target = rtrim(self::IMAGE_UPLOAD_DIR, '/\\') . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            $errors[] = 'Không thể lưu ảnh sản phẩm.';
            return null;
        }

        return url('uploads/products/' . $fileName);
    }

    private function normalizeUploadedFiles(array $files): array {
        if (!is_array($files['name'])) {
            return [$files];
        }

        $normalized = [];
        foreach ($files['name'] as $index => $name) {
            $normalized[] = [
                'name' => $name,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
        }

        return $normalized;
    }

    private function deleteImageFiles(array $images): void {
        foreach ($images as $image) {
            if (preg_match('/^https?:\/\//i', $image)) {
                continue;
            }

            $fileName = basename($image);
            $path = rtrim(self::IMAGE_UPLOAD_DIR, '/\\') . DIRECTORY_SEPARATOR . $fileName;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function imageForDisplay(string $image): string {
        $image = trim($image);
        if ($image === '' || preg_match('/^(https?:)?\/\//i', $image) || str_starts_with($image, '/')) {
            return $image;
        }

        return url($image);
    }

    private function imageForDatabase(string $image): string {
        $image = trim($image);
        $basePath = base_path();

        if ($basePath !== '' && str_starts_with($image, $basePath . '/')) {
            return ltrim(substr($image, strlen($basePath)), '/');
        }

        if (str_starts_with($image, '/uploads/')) {
            return ltrim($image, '/');
        }

        return $image;
    }

    private function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }

    private function notFound(): void {
        http_response_code(404);
        die('Không tìm thấy game!');
    }
}
?>

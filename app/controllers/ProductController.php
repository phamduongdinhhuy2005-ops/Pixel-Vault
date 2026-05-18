<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CategoryModel.php';
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
    private CategoryModel $categories;
    private array $systemOptions = self::FALLBACK_SYSTEM_OPTIONS;

    /**
     * Khởi tạo controller, mở kết nối MySQL và tải các lựa chọn hệ thống.
     */
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();

        if ($this->db === null) {
            http_response_code(500);
            die('Không thể kết nối cơ sở dữ liệu pixel_vault.');
        }

        $this->categories = new CategoryModel($this->db);
        $this->systemOptions = $this->loadSystemOptions();
    }

    /**
     * Hiển thị trang danh sách sản phẩm, có tìm kiếm, lọc và phân trang.
     */
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

    /**
     * Hiển thị trang chi tiết của một sản phẩm theo ID.
     */
    public function detail(int $id): void {
        $product = $this->findProduct($id);

        if ($product === null) {
            $this->notFound();
        }

        include __DIR__ . '/../views/product/detail.php';
    }

    /**
     * Hiển thị form thêm sản phẩm và xử lý lưu sản phẩm mới vào MySQL.
     */
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

    /**
     * Hiển thị form sửa sản phẩm và xử lý cập nhật sản phẩm trong MySQL.
     */
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

    /**
     * Xóa sản phẩm theo ID, đồng thời xóa file ảnh cục bộ nếu có.
     */
    public function delete(int $id): void {
        $product = $this->findProduct($id);

        if ($product !== null) {
            $this->deleteProduct($id);
            $this->deleteImageFiles($product->getImages());
        }

        $this->redirect(url('Product/list'));
    }

    /**
     * Hiển thị trang quản trị gồm danh sách sản phẩm và quản lý danh mục.
     */
    public function admin(): void {
        $products = $this->fetchProducts();
        $categories = $this->categories->allWithProductCount();
        $systemOptions = $this->systemOptions;
        include __DIR__ . '/../views/admin/index.php';
    }

    /**
     * Tải các lựa chọn cho form/filter từ database, fallback nếu bảng trống.
     */
    private function loadSystemOptions(): array {
        $options = self::FALLBACK_SYSTEM_OPTIONS;
        $options['category'] = $this->categories->names() ?: $options['category'];
        $options['genres'] = $this->fetchColumnValues('genres', 'name') ?: $options['genres'];

        return $options;
    }

    /**
     * Lấy một cột dữ liệu cho phép từ bảng genres.
     */
    private function fetchColumnValues(string $table, string $column): array {
        $allowed = [
            'genres' => ['name'],
        ];

        if (!isset($allowed[$table]) || !in_array($column, $allowed[$table], true)) {
            return [];
        }

        $stmt = $this->db->query("SELECT {$column} FROM {$table} ORDER BY id");
        return array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * Lấy toàn bộ sản phẩm từ MySQL và chuyển thành danh sách ProductModel.
     */
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

    /**
     * Tìm một sản phẩm theo ID trong database.
     */
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

    /**
     * Ghép dữ liệu SQL, ảnh và thể loại thành một đối tượng ProductModel.
     */
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

    /**
     * Lấy 3 slot ảnh của sản phẩm, giữ đúng vị trí image_slot 1, 2, 3.
     */
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

    /**
     * Lấy danh sách tên thể loại của một sản phẩm.
     */
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

    /**
     * Tạo sản phẩm mới, lưu thông tin chính, ảnh và thể loại vào MySQL.
     */
    private function createProduct(string $name, string $description, float $price, array $images, array $systemInfo): int {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO products (name, description, price, category_id, product_condition, resolution, rom_format, players, region)
                 VALUES (:name, :description, :price, :category_id, :product_condition, :resolution, :rom_format, :players, :region)'
            );
            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $this->categories->idByName($systemInfo['category']),
                'product_condition' => $systemInfo['condition'],
                'resolution' => $systemInfo['resolution'],
                'rom_format' => $systemInfo['rom_format'],
                'players' => $systemInfo['players'],
                'region' => $systemInfo['region'],
            ]);

            $productId = (int) $this->db->lastInsertId();
            $this->replaceProductImages($productId, $images);
            $this->replaceProductGenres($productId, $systemInfo['genres']);
            $this->db->commit();

            return $productId;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    /**
     * Cập nhật sản phẩm hiện có, bao gồm thông tin chính, ảnh và thể loại.
     */
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
                'category_id' => $this->categories->idByName($systemInfo['category']),
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

    /**
     * Xóa sản phẩm khỏi bảng products.
     */
    private function deleteProduct(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Ghi lại toàn bộ 3 slot ảnh của sản phẩm trong bảng product_images.
     */
    private function replaceProductImages(int $productId, array $images): void {
        $stmt = $this->db->prepare('DELETE FROM product_images WHERE product_id = :product_id');
        $stmt->execute(['product_id' => $productId]);

        $stmt = $this->db->prepare(
            'INSERT INTO product_images (product_id, image_url, image_slot, is_primary)
             VALUES (:product_id, :image_url, :image_slot, :is_primary)'
        );

        $images = array_slice(array_pad($images, self::IMAGE_SLOT_COUNT, ''), 0, self::IMAGE_SLOT_COUNT);

        foreach ($images as $index => $image) {
            if ($image === '') {
                continue;
            }

            $stmt->execute([
                'product_id' => $productId,
                'image_url' => $this->imageForDatabase($image),
                'image_slot' => $index + 1,
                'is_primary' => $index === 0 ? 1 : 0,
            ]);
        }

    }

    /**
     * Ghi lại các thể loại của sản phẩm trong bảng trung gian product_genres.
     */
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

    /**
     * Tìm ID thể loại dựa trên tên thể loại.
     */
    private function genreIdByName(string $name): ?int {
        $stmt = $this->db->prepare('SELECT id FROM genres WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => $name]);
        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    /**
     * Kiểm tra dữ liệu sản phẩm trước khi thêm hoặc sửa.
     */
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

    /**
     * Trả về thông tin hệ thống mặc định cho form thêm sản phẩm.
     */
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

    /**
     * Đọc và làm sạch các tham số lọc sản phẩm từ URL.
     */
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

    /**
     * Chỉ chấp nhận giá trị filter nằm trong danh sách lựa chọn hợp lệ.
     */
    private function validFilterValue(string $field, mixed $value): string {
        $value = trim((string) $value);
        return in_array($value, $this->systemOptions[$field] ?? [], true) ? $value : '';
    }

    /**
     * Lọc danh sách thể loại gửi lên, chỉ giữ các thể loại hợp lệ và không trùng.
     */
    private function validGenres(mixed $genres): array {
        if (!is_array($genres)) {
            $genres = $genres === '' ? [] : [$genres];
        }

        return array_values(array_unique(array_filter(
            array_map(fn($genre) => trim((string) $genre), $genres),
            fn($genre) => in_array($genre, $this->systemOptions['genres'], true)
        )));
    }

    /**
     * Đọc 3 ô đường dẫn ảnh từ form và giữ đúng thứ tự slot.
     */
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

    /**
     * Ghép đường dẫn ảnh cũ với ảnh mới upload; upload ở slot nào thì thay slot đó.
     */
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

    /**
     * Lọc danh sách sản phẩm theo từ khóa, danh mục, thông số và thể loại.
     */
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

    /**
     * Chuyển chuỗi về chữ thường, ưu tiên UTF-8 nếu máy có mbstring.
     */
    private function lowerText(string $text): string {
        return function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    }

    /**
     * Xử lý upload ảnh theo từng slot riêng: ảnh 1, ảnh 2, ảnh 3.
     */
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

    /**
     * Kiểm tra và lưu một file ảnh upload vào thư mục uploads/products.
     */
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

    /**
     * Xóa các file ảnh cục bộ không còn được sản phẩm sử dụng.
     */
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

    /**
     * Chuyển đường dẫn ảnh trong database thành đường dẫn dùng để hiển thị trên web.
     */
    private function imageForDisplay(string $image): string {
        $image = trim($image);
        if ($image === '' || preg_match('/^(https?:)?\/\//i', $image) || str_starts_with($image, '/')) {
            return $image;
        }

        return url($image);
    }

    /**
     * Chuyển đường dẫn ảnh từ form về dạng gọn để lưu trong database.
     */
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

    /**
     * Điều hướng trình duyệt sang URL khác và dừng xử lý hiện tại.
     */
    private function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Trả về lỗi 404 khi không tìm thấy sản phẩm.
     */
    private function notFound(): void {
        http_response_code(404);
        die('Không tìm thấy game!');
    }
}
?>

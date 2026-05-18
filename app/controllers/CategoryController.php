<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class CategoryController {
    private CategoryModel $categories;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();

        if ($db === null) {
            http_response_code(500);
            die('Không thể kết nối cơ sở dữ liệu pixel_vault.');
        }

        $this->categories = new CategoryModel($db);
    }

    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url('Admin'));
        }

        [$name, $description, $errors] = $this->categories->validateInput($_POST);

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            $this->redirect(url('Admin') . '#categories');
        }

        $this->categories->create($name, $description);
        $_SESSION['flash_success'] = 'Đã thêm danh mục mới.';
        $this->redirect(url('Admin') . '#categories');
    }

    public function edit(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url('Admin'));
        }

        [$name, $description, $errors] = $this->categories->validateInput($_POST, $id);

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            $this->redirect(url('Admin') . '#categories');
        }

        $this->categories->update($id, $name, $description);
        $_SESSION['flash_success'] = 'Đã cập nhật danh mục.';
        $this->redirect(url('Admin') . '#categories');
    }

    public function delete(int $id): void {
        $this->categories->delete($id);
        $_SESSION['flash_success'] = 'Đã xóa danh mục. Sản phẩm thuộc danh mục này sẽ chuyển sang trạng thái chưa phân loại.';
        $this->redirect(url('Admin') . '#categories');
    }

    private function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }
}
?>

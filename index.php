<?php
session_start();

require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/controllers/ProductController.php';
require_once __DIR__ . '/app/controllers/CategoryController.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

if ($basePath !== '' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

$path = '/' . trim($path, '/');
$segments = array_values(array_filter(explode('/', $path)));
$section = strtolower($segments[0] ?? '');
$action = strtolower($segments[1] ?? 'list');
$id = isset($segments[2]) ? (int) $segments[2] : 0;

if ($section === 'admin') {
    $productController = new ProductController();
    $productController->admin();
    exit;
}

if ($section === 'product') {
    $productController = new ProductController();

    if ($action === 'add') {
        $productController->add();
    } elseif ($action === 'edit') {
        $productController->edit($id);
    } elseif ($action === 'delete') {
        $productController->delete($id);
    } elseif ($action === 'detail') {
        $productController->detail($id);
    } else {
        $productController->list();
    }

    exit;
}

if ($section === 'category') {
    $categoryController = new CategoryController();

    if ($action === 'add') {
        $categoryController->add();
    } elseif ($action === 'edit') {
        $categoryController->edit($id);
    } elseif ($action === 'delete') {
        $categoryController->delete($id);
    } else {
        $productController = new ProductController();
        $productController->admin();
    }

    exit;
}

$productController = new ProductController();
$productController->list();
?>

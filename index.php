<?php
require_once __DIR__ . '/app/models/ProductModel.php';
session_start();

require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/controllers/ProductController.php';

$controller = new ProductController();
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
    $controller->admin();
    exit;
}

if ($section === 'product') {
    match ($action) {
        'add' => $controller->add(),
        'edit' => $controller->edit($id),
        'delete' => $controller->delete($id),
        'detail' => $controller->detail($id),
        default => $controller->list(),
    };
    exit;
}

$controller->list();
?>

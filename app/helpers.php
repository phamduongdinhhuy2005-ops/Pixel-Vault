<?php
function base_path(): string {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $basePath = str_replace('\\', '/', rtrim(dirname($scriptName), '/\\'));

    return ($basePath === '' || $basePath === '.' || $basePath === '/') ? '' : $basePath;
}

function url(string $path = ''): string {
    $path = ltrim($path, '/');
    $basePath = base_path();

    if ($path === '') {
        return $basePath === '' ? '/' : $basePath;
    }

    return ($basePath === '' ? '' : $basePath) . '/' . $path;
}
?>

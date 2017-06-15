<?php

use app\Models\LogViewer;

$router->get('/', function () {
    require_once __DIR__ . '/../app/Views/main.php';
});

$router->get('/list', function () {
    header('Content-Type: application/json');

    $path = urldecode($_GET['path']);
    $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $last = isset($_GET['last']);

    $file = new LogViewer($path, $page, $limit);
    $result = $file->lines($last)->toJson();

    return $result;
});

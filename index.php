<?php

require_once 'UserService.php';
require_once 'UserController.php';

$controller = new UserController();

if (
    ($_SERVER['REQUEST_METHOD'] === 'POST') ||
    (isset($_GET['view']) && in_array($_GET['view'], ['table', 'thumb']))
) {
    $controller->showUserAction();
    exit;
}

$pageTitle = "Users";

ob_start();

require __DIR__ . '/views/form.php';

$content = ob_get_clean();

require __DIR__ . "/views/layout.php";
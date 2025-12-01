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

?>

<h1 class="page-title">USERS</h1>

<div class="form-wrapper">
    <form method="post" class="filter-form">

        <label>Active:</label>
        <select name="active">
            <option value="">All</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>

        <label>From:</label>
        <input type="text" name="from" placeholder="d/m/Y H:i:s">

        <label>To:</label>
        <input type="text" name="to" placeholder="d/m/Y H:i:s">

        <label>Name starts with:</label>
        <input type="text" name="name">

        <label>Surname starts with:</label>
        <input type="text" name="surname">

        <label>View:</label>
        <select name="view">
            <option value="table">Table</option>
            <option value="thumb">Thumb</option>
        </select>

        <button type="submit" class="btn">Search</button>

    </form>
</div>

<?php

$content = ob_get_clean();

require __DIR__ . "/views/layout.php";
<!-- Page heading and navigation -->
<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campusly | Student Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="index.php">Campusly <span>Admin</span></a>
        <nav>
            <a href="index.php">Students</a>
            <a href="create.php">Add Student</a>
        </nav>
    </header>

    <main class="container">
        <?php show_flash(); ?>

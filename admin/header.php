<?php
$admin_base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
if ($admin_base === '' || $admin_base === '.') {
    $admin_base = '/Admin';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LevelUp Admin Dashboard</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($admin_base) ?>/assets/style.css">
</head>
<body>
<?php

session_start();

require __DIR__ . '/includes/auth.php';

requireLogin();

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,
initial-scale=1.0">

<title>Dashboard</title>

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<div class="auth-card">

<p class="eyebrow">
Protected Area
</p>

<h1>
Welcome,
<?= htmlspecialchars($_SESSION['username']) ?>
</h1>

<p class="subtitle">
You are successfully logged in.
</p>

<div class="account-info">

<p>
<strong>Username:</strong>
<?= htmlspecialchars($_SESSION['username']) ?>
</p>

<p>
<strong>Email:</strong>
<?= htmlspecialchars($_SESSION['email']) ?>
</p>

</div>

<a class="button-link"
href="logout.php">
Logout
</a>

</div>

</body>
</html>

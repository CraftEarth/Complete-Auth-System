<?php

session_start();

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/csrf.php';

if (isLoggedIn()) {

    header(
        'Location: dashboard.php'
    );

    exit;
}

$error = '';

$login = '';

if (
    $_SERVER['REQUEST_METHOD']
    === 'POST'
) {

    if (!verifyCsrf()) {

        $error =
            'Invalid request. Please try again.';

    } else {

        $login =
            trim(
                $_POST['login'] ?? ''
            );

        $password =
            $_POST['password'] ?? '';

        if (
            $login === '' ||
            $password === ''
        ) {

            $error =
                'Enter your username/email and password.';

        } else {

            $stmt =
                $pdo->prepare(
                    'SELECT
                        id,
                        username,
                        email,
                        password
                     FROM users
                     WHERE username = ?
                        OR email = ?
                     LIMIT 1'
                );

            $stmt->execute([
                $login,
                $login
            ]);

            $user =
                $stmt->fetch();

            if (
                $user &&
                password_verify(
                    $password,
                    $user['password']
                )
            ) {

                loginUser($user);

                header(
                    'Location: dashboard.php'
                );

                exit;
            }

            $error =
                'Invalid username/email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,
initial-scale=1.0">

<title>Login</title>

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<div class="auth-card">

<p class="eyebrow">
Complete Authentication System
</p>

<h1>Welcome Back</h1>

<p class="subtitle">
Sign in to your account.
</p>

<?php if ($error): ?>

<div class="error">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>

<form method="POST">

<?= csrfField() ?>

<label>

Username or Email

<input
type="text"
name="login"
value="<?= htmlspecialchars($login) ?>"
autocomplete="username"
required>

</label>

<label>

Password

<input
type="password"
name="password"
autocomplete="current-password"
required>

</label>

<button type="submit">
Sign In
</button>

</form>

<div class="links">

<a href="register.php">
Create Account
</a>

<a href="forgot-password.php">
Forgot Password?
</a>

</div>

</div>

</body>
</html>

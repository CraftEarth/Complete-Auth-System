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

$errors = [];

$success = '';

$username = '';

$email = '';

if (
    $_SERVER['REQUEST_METHOD']
    === 'POST'
) {

    if (!verifyCsrf()) {

        $errors[] =
            'Invalid request. Please try again.';

    } else {

        $username =
            trim(
                $_POST['username']
                ?? ''
            );

        $email =
            trim(
                $_POST['email']
                ?? ''
            );

        $password =
            $_POST['password']
            ?? '';

        $confirmPassword =
            $_POST['confirm_password']
            ?? '';

        if (
            strlen($username) < 3
        ) {

            $errors[] =
                'Username must be at least 3 characters.';
        }

        if (
            !preg_match(
                '/^[A-Za-z0-9_]+$/',
                $username
            )
        ) {

            $errors[] =
                'Username may only contain letters, numbers, and underscores.';
        }

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $errors[] =
                'Enter a valid email address.';
        }

        if (
            strlen($password) < 8
        ) {

            $errors[] =
                'Password must be at least 8 characters.';
        }

        if (
            $password !==
            $confirmPassword
        ) {

            $errors[] =
                'Passwords do not match.';
        }

        if (!$errors) {

            $stmt =
                $pdo->prepare(
                    'SELECT id
                     FROM users
                     WHERE username = ?
                        OR email = ?
                     LIMIT 1'
                );

            $stmt->execute([
                $username,
                $email
            ]);

            if ($stmt->fetch()) {

                $errors[] =
                    'That username or email is already registered.';
            }
        }

        if (!$errors) {

            $hash =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

            $stmt =
                $pdo->prepare(
                    'INSERT INTO users
                    (username, email, password)
                    VALUES (?, ?, ?)'
                );

            $stmt->execute([
                $username,
                $email,
                $hash
            ]);

            $success =
                'Account created successfully. You can now sign in.';

            $username = '';
            $email = '';
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

<title>Create Account</title>

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<div class="auth-card">

<p class="eyebrow">
Complete Authentication System
</p>

<h1>Create Account</h1>

<p class="subtitle">
Register a new account.
</p>

<?php if ($errors): ?>

<div class="error">

<ul>

<?php foreach ($errors as $error): ?>

<li>
<?= htmlspecialchars($error) ?>
</li>

<?php endforeach; ?>

</ul>

</div>

<?php endif; ?>

<?php if ($success): ?>

<div class="success">

<?= htmlspecialchars($success) ?>

</div>

<?php endif; ?>

<form method="POST">

<?= csrfField() ?>

<label>

Username

<input
type="text"
name="username"
value="<?= htmlspecialchars($username) ?>"
autocomplete="username"
required>

</label>

<label>

Email

<input
type="email"
name="email"
value="<?= htmlspecialchars($email) ?>"
autocomplete="email"
required>

</label>

<label>

Password

<input
type="password"
name="password"
autocomplete="new-password"
required>

</label>

<label>

Confirm Password

<input
type="password"
name="confirm_password"
autocomplete="new-password"
required>

</label>

<button type="submit">
Create Account
</button>

</form>

<div class="links">

<a href="index.php">
Already registered? Sign In
</a>

</div>

</div>

</body>
</html>

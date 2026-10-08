<?php

session_start();

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/csrf.php';

$token =
    $_GET['token']
    ??
    $_POST['token']
    ??
    '';

$error = '';

$success = '';

$reset = null;

if ($token === '') {

    $error =
        'Invalid reset link.';
}

if (!$error) {

    $tokenHash =
        hash(
            'sha256',
            $token
        );

    $stmt =
        $pdo->prepare(
            'SELECT
                id,
                user_id,
                expires_at,
                used
             FROM password_resets
             WHERE token_hash = ?
             LIMIT 1'
        );

    $stmt->execute([
        $tokenHash
    ]);

    $reset =
        $stmt->fetch();

    if (!$reset) {

        $error =
            'Invalid reset link.';

    } elseif (
        (int)$reset['used'] === 1
    ) {

        $error =
            'This reset link has already been used.';

    } elseif (
        strtotime(
            $reset['expires_at']
        ) < time()
    ) {

        $error =
            'This reset link has expired.';
    }
}

if (
    $_SERVER['REQUEST_METHOD']
    === 'POST'
    &&
    !$error
    &&
    $reset
) {

    if (!verifyCsrf()) {

        $error =
            'Invalid request. Please try again.';

    } else {

        $password =
            $_POST['password']
            ?? '';

        $confirm =
            $_POST['confirm_password']
            ?? '';

        if (
            strlen($password) < 8
        ) {

            $error =
                'Password must be at least 8 characters.';

        } elseif (
            $password !== $confirm
        ) {

            $error =
                'Passwords do not match.';

        } else {

            $hash =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

            $pdo->beginTransaction();

            try {

                $stmt =
                    $pdo->prepare(
                        'UPDATE users
                         SET password = ?
                         WHERE id = ?'
                    );

                $stmt->execute([
                    $hash,
                    $reset['user_id']
                ]);

                $stmt =
                    $pdo->prepare(
                        'UPDATE password_resets
                         SET used = 1
                         WHERE id = ?'
                    );

                $stmt->execute([
                    $reset['id']
                ]);

                $pdo->commit();

                $success =
                    'Password updated successfully.';

            } catch (Throwable $e) {

                $pdo->rollBack();

                $error =
                    'Unable to update password.';
            }
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

<title>Reset Password</title>

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<div class="auth-card">

<p class="eyebrow">
Account Recovery
</p>

<h1>Reset Password</h1>

<?php if ($error): ?>

<div class="error">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>

<?php if ($success): ?>

<div class="success">

<?= htmlspecialchars($success) ?>

</div>

<a class="button-link"
href="index.php">
Go to Login
</a>

<?php elseif (!$error): ?>

<form method="POST">

<?= csrfField() ?>

<input
type="hidden"
name="token"
value="<?= htmlspecialchars($token) ?>">

<label>

New Password

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
Reset Password
</button>

</form>

<?php endif; ?>

</div>

</body>
</html>

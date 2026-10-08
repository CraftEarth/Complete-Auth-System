<?php

session_start();

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/csrf.php';

$config =
    require __DIR__ . '/includes/config.php';

$message = '';

$resetLink = '';

if (
    $_SERVER['REQUEST_METHOD']
    === 'POST'
) {

    if (!verifyCsrf()) {

        $message =
            'Invalid request. Please try again.';

    } else {

        $email =
            trim(
                $_POST['email']
                ?? ''
            );

        if (
            filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $stmt =
                $pdo->prepare(
                    'SELECT id
                     FROM users
                     WHERE email = ?
                     LIMIT 1'
                );

            $stmt->execute([
                $email
            ]);

            $user =
                $stmt->fetch();

            if ($user) {

                $token =
                    bin2hex(
                        random_bytes(32)
                    );

                $tokenHash =
                    hash(
                        'sha256',
                        $token
                    );

                $expiresAt =
                    date(
                        'Y-m-d H:i:s',
                        time() + 3600
                    );

                $pdo->prepare(
                    'UPDATE password_resets
                     SET used = 1
                     WHERE user_id = ?
                     AND used = 0'
                )->execute([
                    $user['id']
                ]);

                $stmt =
                    $pdo->prepare(
                        'INSERT INTO
                        password_resets
                        (
                            user_id,
                            token_hash,
                            expires_at
                        )
                        VALUES (?, ?, ?)'
                    );

                $stmt->execute([
                    $user['id'],
                    $tokenHash,
                    $expiresAt
                ]);

                $resetLink =
                    $config['app']['base_url']
                    .
                    '/reset-password.php?token='
                    .
                    urlencode($token);
            }
        }

        $message =
            'If that email exists, a password reset request has been created.';
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

<title>Forgot Password</title>

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<div class="auth-card">

<p class="eyebrow">
Account Recovery
</p>

<h1>Forgot Password</h1>

<p class="subtitle">
Enter your email address to request a reset link.
</p>

<?php if ($message): ?>

<div class="message">

<?= htmlspecialchars($message) ?>

</div>

<?php endif; ?>

<form method="POST">

<?= csrfField() ?>

<label>

Email Address

<input
type="email"
name="email"
autocomplete="email"
required>

</label>

<button type="submit">
Request Reset
</button>

</form>

<?php if ($resetLink): ?>

<div class="dev-box">

<strong>
Local Development Link
</strong>

<p>
In production this link would normally be sent by email.
</p>

<a href="<?= htmlspecialchars($resetLink) ?>">
Reset Password
</a>

</div>

<?php endif; ?>

<div class="links">

<a href="index.php">
Back to Login
</a>

</div>

</div>

</body>
</html>

<?php

session_start();

require __DIR__ . '/includes/auth.php';

logoutUser();

header(
    'Location: index.php'
);

exit;

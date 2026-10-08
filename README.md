# Complete Authentication System

A reusable PHP and MySQL authentication starter kit.

This project combines the separate Login System, Registration System,
and Forgot Password System into one standalone package.

## Features

- User registration
- Username and email validation
- Duplicate account protection
- Secure password hashing
- Login by username or email
- PHP sessions
- Session regeneration after login
- Protected dashboard
- Logout
- Forgot-password workflow
- Secure random reset tokens
- SHA-256 token storage
- One-hour token expiration
- Single-use reset links
- CSRF protection
- PDO prepared statements
- Responsive interface

## Project Structure

complete-auth-system/

assets/
    css/
        style.css

includes/
    auth.php
    config.php
    csrf.php
    db.php

sql/
    schema.sql

dashboard.php
forgot-password.php
index.php
logout.php
register.php
reset-password.php
README.md

## Installation

### 1. Import the database

Import:

sql/schema.sql

This creates:

complete_auth_demo

with:

users
password_resets

### 2. Configure database access

Edit:

includes/config.php

Default local XAMPP configuration:

host: 127.0.0.1
database: complete_auth_demo
username: root
password: blank

### 3. Open the application

http://localhost/projects/complete-auth-system/

## Test Flow

1. Register a new account
2. Login
3. Confirm dashboard protection
4. Logout
5. Request a password reset
6. Open the generated development reset link
7. Change the password
8. Login using the new password

## Production Notes

The reset link is displayed directly during local development.

For production use:

- Send the reset link through email instead
- Enable HTTPS
- Configure secure session cookies
- Use a dedicated database account
- Add login rate limiting
- Add email verification
- Add audit logging where needed
- Never expose database credentials publicly

## Security Features

Passwords use PHP password_hash().

Authentication uses password_verify().

Database queries use PDO prepared statements.

Sessions regenerate IDs after login.

Password-reset tokens are generated with random_bytes().

Only SHA-256 hashes of password-reset tokens are stored.

Reset links expire after one hour and can only be used once.

Forms include CSRF protection.

Forgot-password responses do not reveal whether an email address exists.

## Author

William Murphy / CraftEarth

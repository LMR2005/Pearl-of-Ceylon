<?php

function sanitize($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isUser()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

function isClerk()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'clerk';
}

function isManager()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'manager';
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit();
    }
}

function requireRole($roles = [])
{
    if (!in_array($_SESSION['role'], $roles)) {
        header("HTTP/1.1 403 Forbidden");
        echo "Access denied.";
        exit();
    }
}

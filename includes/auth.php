<?php
require_once 'functions.php';

function checkAccess($requiredRoles)
{
    requireLogin();
    if (!in_array($_SESSION['role'], $requiredRoles)) {
        header('HTTP/1.1 403 Forbidden');
        echo "You do not have permission to access this page.";
        exit;
    }
}

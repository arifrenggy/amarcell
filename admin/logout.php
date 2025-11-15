<?php
require_once __DIR__ . '/../config/config.php';

// Hapus semua session data
session_unset();
session_destroy();

// Redirect ke halaman login
header('Location: login.php');
exit();
?>
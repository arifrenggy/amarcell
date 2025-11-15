<?php
// This header should be included on all admin pages.
// It sets up the HTML document, includes CSS, and starts the body and sidebar.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= $page_title ?? 'Amar Cell' ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        /* Core Layout Styles */
        body {
            background-color: #f8fafc;
        }
        .sidebar {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
        }
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            padding-top: 70px; /* Space for the fixed navbar */
        }
        .navbar-top {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-left: 250px;
            position: fixed; 
            width: calc(100% - 250px); 
            z-index: 999;
            top: 0;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: none;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

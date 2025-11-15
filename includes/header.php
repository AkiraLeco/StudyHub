<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>StudyHub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <?php
    $page = basename($_SERVER['PHP_SELF']);
    if ($page === 'index.php') {
        echo '<link rel="stylesheet" href="assets/css/index.css">';
    } elseif ($page === 'notas.php') {
        echo '<link rel="stylesheet" href="assets/css/notas.css">';
    }
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="site-header">
        <span class="site-title">StudyHub</span>
        <nav class="site-nav">
            <a href="index.php">Início</a>
            <a href="#">Músicas</a>
            <a href="notas.php">Bloco de Notas</a>
            <a href="#">Cards</a>
        </nav>
        <span class="site-user">Usuário: <strong><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Visitante'; ?></strong></span>
    </div>
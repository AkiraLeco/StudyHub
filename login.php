<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $_SESSION['username'] = $username;

    // Salva no banco se não existir
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nome = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome) VALUES (?)");
        $stmt->execute([$username]);
    }

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>StudyHub - Entrada</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #74b9ff 0%, #a29bfe 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .entrada-container {
            position: relative;
            overflow: hidden;
            background: #fff;
            padding: 2rem 2.2rem 1.7rem 2.2rem;
            border-radius: 16px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.10);
            text-align: center;
            min-width: 320px;
            max-width: 350px;
            min-height: 290px;
            max-height: 380px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .card-bg-svg {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            border-radius: 16px;
            overflow: hidden;
            pointer-events: none;
            background: url('assets/img/2addddd3-8aa0-4f88-bea1-1b8548ec78c2.png') center center/cover no-repeat;
            filter: blur(18px) brightness(1.15) saturate(1.2);
            opacity: 0.85;
        }
        .entrada-container > *:not(.card-bg-svg) {
            position: relative;
            z-index: 1;
        }
        .entrada-container h1 {
            margin-bottom: 0.3rem;
            color: #2d3436;
            font-size: 1.7rem;
            font-weight: 700;
        }
        .entrada-container p {
            margin-bottom: 1.2rem;
            color: #636e72;
            font-size: 1rem;
        }
        .entrada-container input {
            width: 92%;
            padding: 0.7rem;
            margin-bottom: 1.1rem;
            border: 1px solid #dfe6e9;
            border-radius: 6px;
            font-size: 1rem;
            background: rgba(245, 246, 250, 0.7);
            transition: border 0.2s, background 0.2s;
        }
        .entrada-container input:focus {
            border: 1.5px solid #0984e3;
            outline: none;
            background: rgba(245, 246, 250, 0.9);
        }
        .entrada-container button {
            background: linear-gradient(90deg, #0984e3 60%, #6c5ce7 100%);
            color: #fff;
            border: none;
            padding: 0.7rem 2.2rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.05rem;
            font-weight: 500;
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(9,132,227,0.08);
        }
        .entrada-container button:hover {
            background: linear-gradient(90deg, #74b9ff 60%, #a29bfe 100%);
        }
        .entrada-container form {
            margin-top: 1.2rem;
        }
        @media (max-width: 400px) {
            .entrada-container {
                min-width: unset;
                width: 95vw;
                padding: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-animated-svg"></div>
    <div class="entrada-container">
        <div class="card-bg-svg"></div>
        <h1>Bem-vindo ao StudyHub</h1>
        <p>
            Seu espaço para estudar, relaxar e se organizar.<br>
            Digite seu nome para começar!
        </p>
        <form method="post">
            <input type="text" name="username" placeholder="Seu nome" required autocomplete="off">
            <br>
            <button type="submit">Começar</button>
        </form>
    </div>
</body>
</html>
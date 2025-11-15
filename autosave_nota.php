<?php
session_start();
require_once 'includes/db.php';

$username = $_SESSION['username'] ?? null;
if (!$username) exit;

$conteudo = $_POST['conteudo'] ?? '';
if ($conteudo === '') exit;

// Atualiza a nota mais recente do usuário (ou crie lógica para nota específica)
$stmt = $pdo->prepare("UPDATE notas SET conteudo = ?, criado_em = NOW() WHERE usuario = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$conteudo, $username]);
echo 'ok';
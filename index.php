<?php
session_start();
include 'includes/header.php';
require_once 'includes/db.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Visitante';

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nome = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
?>

<div class="main-content">
    <?php include 'includes/sidebar.php'; ?>
    <main class="dashboard">
        <section class="welcome">
            <h1>Olá, <?php echo htmlspecialchars($username); ?>!</h1>
            <?php if ($user): ?>
                <p class="subtitle">
                    Seu cadastro foi realizado em: <?php echo date('d/m/Y H:i', strtotime($user['criado_em'])); ?>
                </p>
            <?php endif; ?>
            <form action="login.php" method="get">
                <button type="submit" class="trocar-btn">Trocar usuário</button>
            </form>
        </section>
        <section class="features">
            <h2>O que você pode fazer aqui:</h2>
            <ul>
                <li>
                    <span class="feature-icon">🎵</span>
                    <span class="feature-content">
                        <span class="feature-title">Músicas para concentração:</span>
                        <span class="feature-desc">escute playlists para estudar melhor.</span>
                    </span>
                </li>
                <li>
                    <span class="feature-icon">📝</span>
                    <span class="feature-content">
                        <span class="feature-title">Anotações inteligentes:</span>
                        <span class="feature-desc">organize ideias e resumos.</span>
                    </span>
                </li>
                <li>
                    <span class="feature-icon">📚</span>
                    <span class="feature-content">
                        <span class="feature-title">Cards de estudo:</span>
                        <span class="feature-desc">crie e revise conteúdos importantes.</span>
                    </span>
                </li>
            </ul>
        </section>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
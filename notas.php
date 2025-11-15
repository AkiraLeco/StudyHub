<?php
session_start();
require_once 'includes/db.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
if (!$username) {
    header('Location: login.php');
    exit;
}

// Salvar título da nota (deve vir antes de qualquer saída HTML)
if (isset($_GET['titulo'])) {
    $nota_id = intval($_GET['titulo']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo_nota'])) {
        $titulo = trim($_POST['titulo_nota']);
        $stmt = $pdo->prepare("UPDATE notas SET titulo = ? WHERE id = ? AND usuario = ?");
        $stmt->execute([$titulo, $nota_id, $username]);
        header('Location: notas.php');
        exit;
    }
}

// Adicionar nota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_nota'])) {
    $conteudo = trim($_POST['nova_nota']);
    if ($conteudo !== '') {
        $stmt = $pdo->prepare("INSERT INTO notas (usuario, conteudo) VALUES (?, ?)");
        $stmt->execute([$username, $conteudo]);
        $nota_id = $pdo->lastInsertId();
        header('Location: notas.php?titulo=' . $nota_id);
        exit;
    }
    header('Location: notas.php');
    exit;
}

// Apagar nota
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $stmt = $pdo->prepare("DELETE FROM notas WHERE id = ? AND usuario = ?");
    $stmt->execute([$id, $username]);
    header('Location: notas.php');
    exit;
}

// Buscar notas do usuário
$stmt = $pdo->prepare("SELECT * FROM notas WHERE usuario = ? ORDER BY criado_em DESC");
$stmt->execute([$username]);
$notas = $stmt->fetchAll();

include 'includes/header.php';
?>

<main class="dashboard">
    <h2 style="text-align:center;">Bloco de Notas</h2>

    <?php if (isset($_GET['titulo'])): ?>
        <form method="post" style="max-width:400px;margin:2rem auto;text-align:center;">
            <label for="titulo_nota" style="font-weight:600;font-size:1.1rem;">Dê um título para sua nota:</label><br>
            <input type="text" name="titulo_nota" id="titulo_nota" maxlength="255" style="width:100%;padding:0.7rem;margin:1rem 0;border-radius:8px;border:1px solid #dfe6e9;" required>
            <br>
            <button type="submit" class="nota-btn">Salvar título</button>
        </form>
    <?php endif; ?>

    <div class="nota-editor-layout">
        <div class="nota-folha-container">
            <form method="post" onsubmit="return salvarNota()">
                <div id="nota-editor" class="nota-folha" contenteditable="true" spellcheck="true"></div>
                <input type="hidden" name="nova_nota" id="nova_nota">
                <button type="submit" class="nota-btn">Salvar nota</button>
            </form>
        </div>
        <div class="nota-toolbar-card">
            <!-- Checkbox de salvamento automático em cima do card -->
            <label style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem;">
                <input type="checkbox" id="autosave-toggle" checked>
                Salvamento automático
            </label>
            <div class="nota-editor-toolbar">
                <!-- Grupo: Formatação -->
                <div class="nota-toolbar-group">
                    <button type="button" onclick="formatar('bold', this)" title="Negrito"><b>B</b></button>
                    <button type="button" onclick="formatar('italic', this)" title="Itálico"><i>I</i></button>
                    <button type="button" onclick="formatar('underline', this)" title="Sublinhado"><u>U</u></button>
                    <button type="button" onclick="formatar('strikeThrough', this)" title="Tachado"><s>S</s></button>
                </div>
                <!-- Grupo: Listas -->
                <div class="nota-toolbar-group">
                    <button type="button" onclick="formatar('insertUnorderedList', this)" title="Lista">&#8226; Lista</button>
                    <button type="button" onclick="formatar('insertOrderedList', this)" title="Lista numerada">1. Lista</button>
                </div>
                <!-- Grupo: Alinhamento -->
                <div class="nota-toolbar-group">
                    <button type="button" onclick="formatar('justifyLeft', this)" data-cmd="justifyLeft" title="Alinhar à esquerda" class="align-btn">
                        <svg width="32" height="32" viewBox="0 0 32 32">
                            <rect x="6" y="8" width="20" height="2" rx="1" fill="#222"/>
                            <rect x="6" y="13" width="14" height="2" rx="1" fill="#222"/>
                            <rect x="6" y="18" width="20" height="2" rx="1" fill="#222"/>
                            <rect x="6" y="23" width="14" height="2" rx="1" fill="#222"/>
                        </svg>
                    </button>
                    <button type="button" onclick="formatar('justifyCenter', this)" data-cmd="justifyCenter" title="Centralizar" class="align-btn">
                        <svg width="32" height="32" viewBox="0 0 32 32">
                            <rect x="8" y="8" width="16" height="2" rx="1" fill="#222"/>
                            <rect x="11" y="13" width="10" height="2" rx="1" fill="#222"/>
                            <rect x="8" y="18" width="16" height="2" rx="1" fill="#222"/>
                            <rect x="11" y="23" width="10" height="2" rx="1" fill="#222"/>
                        </svg>
                    </button>
                    <button type="button" onclick="formatar('justifyRight', this)" data-cmd="justifyRight" title="Alinhar à direita" class="align-btn">
                        <svg width="32" height="32" viewBox="0 0 32 32">
                            <rect x="6" y="8" width="20" height="2" rx="1" fill="#222"/>
                            <rect x="12" y="13" width="14" height="2" rx="1" fill="#222"/>
                            <rect x="6" y="18" width="20" height="2" rx="1" fill="#222"/>
                            <rect x="12" y="23" width="14" height="2" rx="1" fill="#222"/>
                        </svg>
                    </button>
                    <button type="button" onclick="formatar('justifyFull', this)" data-cmd="justifyFull" title="Justificar" class="align-btn">
                        <svg width="32" height="32" viewBox="0 0 32 32">
                            <rect x="6" y="8" width="20" height="2" rx="1" fill="#222"/>
                            <rect x="6" y="13" width="20" height="2" rx="1" fill="#222"/>
                            <rect x="6" y="18" width="20" height="2" rx="1" fill="#222"/>
                            <rect x="6" y="23" width="20" height="2" rx="1" fill="#222"/>
                        </svg>
                    </button>
                </div>
                <!-- Grupo: Cor e Grifar -->
                <div class="nota-toolbar-group">
                    <!-- Grifar -->
                    <div class="color-dropdown" id="highlight-dropdown">
                        <button type="button" onclick="toggleHighlightMenu()" class="color-btn" id="highlight-btn" title="Grifar">
                            <svg width="20" height="20" viewBox="0 0 20 20">
                                <rect x="4" y="15" width="12" height="3" fill="#ffff00"/>
                                <polygon points="10,4 16,15 4,15" fill="#888"/>
                            </svg>
                            <span class="arrow">&#9660;</span>
                        </button>
                        <div class="color-menu" id="highlight-menu">
                            <button type="button" class="color-option" style="background:#ffff00" onclick="setHighlight('#ffff00')"></button>
                            <button type="button" class="color-option" style="background:#00ff00" onclick="setHighlight('#00ff00')"></button>
                            <button type="button" class="color-option" style="background:#00ffff" onclick="setHighlight('#00ffff')"></button>
                            <button type="button" class="color-option" style="background:#ff00ff" onclick="setHighlight('#ff00ff')"></button>
                            <button type="button" class="color-option" style="background:#ffa500" onclick="setHighlight('#ffa500')"></button>
                            <button type="button" class="color-option" style="background:#fff" onclick="setHighlight('#fff')"></button>
                            <button type="button" class="color-option" style="background:#000" onclick="setHighlight('#000')"></button>
                        </div>
                    </div>
                    <!-- Cor do texto -->
                    <div class="color-dropdown" id="color-dropdown">
                        <button type="button" onclick="toggleColorMenu()" class="color-btn" id="color-btn" title="Cor do texto">
                            <svg width="20" height="20" viewBox="0 0 20 20"><text x="4" y="16" font-size="14" fill="#e74c3c">A</text></svg>
                            <span class="arrow">&#9660;</span>
                        </button>
                        <div class="color-menu" id="color-menu">
                            <button type="button" class="color-option" style="background:#e74c3c" onclick="setColor('#e74c3c')"></button>
                            <button type="button" class="color-option" style="background:#0984e3" onclick="setColor('#0984e3')"></button>
                            <button type="button" class="color-option" style="background:#00b894" onclick="setColor('#00b894')"></button>
                            <button type="button" class="color-option" style="background:#f1c40f" onclick="setColor('#f1c40f')"></button>
                            <button type="button" class="color-option" style="background:#222" onclick="setColor('#222')"></button>
                            <button type="button" class="color-option" style="background:#000" onclick="setColor('#000')"></button>
                        </div>
                    </div>
                </div>
                <!-- Grupo: Tamanho e desfazer/refazer -->
                <div class="nota-toolbar-group">
                    <select id="font-size-select" class="font-size-select" onchange="setFontSize(this.value)" title="Tamanho da fonte">
                        <option value="3">Normal</option>
                        <option value="1">Muito pequeno</option>
                        <option value="2">Pequeno</option>
                        <option value="4">Grande</option>
                        <option value="5">Muito grande</option>
                        <option value="6">Enorme</option>
                        <option value="7">Máximo</option>
                    </select>
                    <button type="button" onclick="formatar('undo', this)" title="Desfazer">&#8630;</button>
                    <button type="button" onclick="formatar('redo', this)" title="Refazer">&#8631;</button>
                </div>
            </div>
        </div>
    </div>
    <div>
        <?php foreach ($notas as $nota): ?>
            <div style="background:#f4f8fb;border-radius:8px;padding:1rem;margin-bottom:1rem;position:relative;">
                <?php if (!empty($nota['titulo'])): ?>
                    <div style="font-weight:bold;font-size:1.1rem;margin-bottom:0.5rem;"><?php echo htmlspecialchars($nota['titulo']); ?></div>
                <?php endif; ?>
                <div style="white-space:pre-wrap;"><?php echo $nota['conteudo']; ?></div>
                <small style="color:#636e72;"><?php echo date('d/m/Y H:i', strtotime($nota['criado_em'])); ?></small>
                <a href="?del=<?php echo $nota['id']; ?>" style="position:absolute;top:8px;right:12px;color:#d63031;text-decoration:none;font-size:1.2rem;" title="Apagar nota" onclick="return confirm('Apagar esta nota?')">&times;</a>
            </div>
        <?php endforeach; ?>
        <?php if (empty($notas)): ?>
            <p style="color:#636e72;">Nenhuma nota ainda.</p>
        <?php endif; ?>
    </div>
</main>

<script src="assets/js/main.js"></script>
<?php include 'includes/footer.php'; ?>
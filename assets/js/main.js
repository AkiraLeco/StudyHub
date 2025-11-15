// Sidebar toggle de seções
function toggleSection(section) {
    document.querySelectorAll('.sidebar-section').forEach(div => div.style.display = 'none');
    if (section === 'clock') document.getElementById('clock-section').style.display = 'block';
    if (section === 'timer') document.getElementById('timer-section').style.display = 'block';
    if (section === 'motivation') document.getElementById('motivation-section').style.display = 'block';
}

// --- Abrir/fechar cards ---
function toggleClockCard() { toggleToolCard('clock-card'); }
function toggleTimerCard() { toggleToolCard('timer-card'); }
function toggleMotivationCard() { toggleToolCard('motivation-card'); }
function toggleToolCard(cardId) {
    const card = document.getElementById(cardId);
    if (card.style.display === 'none' || card.style.display === '') {
        document.querySelectorAll('.tool-card').forEach(c => c.style.display = 'none');
        card.style.display = 'flex';
    } else {
        card.style.display = 'none';
    }
}

// Drag & drop para todos os cards (exceto no canto de resize)
['clock-card', 'timer-card', 'motivation-card'].forEach(cardId => {
    const card = document.getElementById(cardId);
    if (!card) return;
    let isDragging = false, offsetX = 0, offsetY = 0;

    card.addEventListener('mousedown', function(e) {
        const resizeArea = 18;
        const rect = card.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;
        if (mouseX > rect.width - resizeArea && mouseY > rect.height - resizeArea) return;
        isDragging = true;
        offsetX = e.clientX - card.offsetLeft;
        offsetY = e.clientY - card.offsetTop;
        function drag(e2) {
            if (!isDragging) return;
            card.style.left = (e2.clientX - offsetX) + 'px';
            card.style.top = (e2.clientY - offsetY) + 'px';
            card.style.right = 'auto';
            card.style.bottom = 'auto';
        }
        function stopDrag() {
            isDragging = false;
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', stopDrag);
        }
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', stopDrag);
    });
});

// Relógio
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('clock');
    if (el) el.textContent = `${h}:${m}:${s}`;
}
setInterval(updateClock, 1000);
updateClock();

// Cronômetro com milissegundos
let timerInterval, timerStart = null, timerElapsed = 0, timerRunning = false;
function startTimer() {
    if (timerRunning) return;
    timerRunning = true;
    timerStart = Date.now() - timerElapsed;
    timerInterval = setInterval(displayTimer, 10);
}
function pauseTimer() {
    if (!timerRunning) return;
    timerRunning = false;
    clearInterval(timerInterval);
    timerElapsed = Date.now() - timerStart;
}
function resetTimer() {
    timerRunning = false;
    clearInterval(timerInterval);
    timerStart = null;
    timerElapsed = 0;
    displayTimer();
}
function displayTimer() {
    let elapsed = timerRunning ? Date.now() - timerStart : timerElapsed;
    let ms = Math.floor((elapsed % 1000) / 10);
    let s = Math.floor((elapsed / 1000) % 60);
    let m = Math.floor((elapsed / (1000 * 60)) % 60);
    let h = Math.floor((elapsed / (1000 * 60 * 60)));
    const timerEl = document.getElementById('timer');
    if (timerEl) {
        timerEl.textContent =
            `${String(h).padStart(2, '0')}:` +
            `${String(m).padStart(2, '0')}:` +
            `${String(s).padStart(2, '0')}.` +
            `${String(ms).padStart(2, '0')}`;
    }
}
displayTimer();

// Motivação do dia
const frases = [
    "Acredite no seu potencial!",
    "Cada dia é uma nova chance de aprender.",
    "Estudar é investir em você mesmo.",
    "O sucesso é a soma de pequenos esforços diários.",
    "Você é capaz de ir além!"
];
function showMotivation() {
    const idx = Math.floor(Math.random() * frases.length);
    const el = document.getElementById('motivation');
    if (el) el.textContent = frases[idx];
}
showMotivation();

// Sidebar encolher/expandir
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) sidebar.classList.toggle('sidebar-collapsed');
}

// --- Editor de notas ---

const editor = document.getElementById('nota-editor');
const autosaveToggle = document.getElementById('autosave-toggle');
let autosaveEnabled = true;
let autosaveTimer = null;
let lastContent = '';

// Destaque do editor ao focar
if (editor) {
    editor.addEventListener('focus', function() {
        editor.classList.add('ativo');
    });
    editor.addEventListener('blur', function() {
        editor.classList.remove('ativo');
    });

    editor.addEventListener('input', function() {
        if (autosaveEnabled) {
            if (autosaveTimer) clearTimeout(autosaveTimer);
            autosaveTimer = setTimeout(autosave, 1500);
        }
    });
}

// Ativa/desativa autosave pelo botão
if (autosaveToggle) {
    autosaveToggle.addEventListener('change', function() {
        autosaveEnabled = this.checked;
        if (!autosaveEnabled && autosaveTimer) {
            clearTimeout(autosaveTimer);
            autosaveTimer = null;
        }
    });
}

// Autosave AJAX
function autosave() {
    if (!autosaveEnabled || !editor) return;
    const content = editor.innerHTML;
    if (content !== lastContent) {
        fetch('autosave_nota.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'conteudo=' + encodeURIComponent(content)
        });
        lastContent = content;
    }
}

// Formatação, cor, grifar, alinhamento, listas
function formatar(cmd, btn, value = null) {
    if (!editor) return;
    editor.focus();
    setTimeout(() => {
        if (value !== null) {
            document.execCommand(cmd, false, value);
        } else {
            document.execCommand(cmd, false, null);
        }

        // Não marca como ativo os botões de cor/grifar/lista
        if (
            cmd === 'insertUnorderedList' ||
            cmd === 'insertOrderedList' ||
            cmd === 'foreColor' ||
            cmd === 'hiliteColor'
        ) {
            return;
        }

        // Alinhamentos: só um pode ficar ativo
        const alignCmds = ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'];
        if (alignCmds.includes(cmd)) {
            document.querySelectorAll('.nota-editor-toolbar button').forEach(b => {
                if (alignCmds.includes(b.getAttribute('data-cmd'))) {
                    b.classList.remove('ativo');
                }
            });
            if (document.queryCommandState(cmd)) {
                btn.classList.add('ativo');
            }
            return;
        }

        // Formatação múltipla (negrito, itálico, etc.)
        if (document.queryCommandState(cmd)) {
            btn.classList.add('ativo');
        } else {
            btn.classList.remove('ativo');
        }
    }, 1);
}

// Salvar nota manual
function salvarNota() {
    if (!editor) return false;
    document.getElementById('nova_nota').value = editor.innerHTML;
    return true;
}

// Dropdown de cor do texto
function toggleColorMenu() {
    saveSelection();
    const colorDropdown = document.getElementById('color-dropdown');
    const highlightDropdown = document.getElementById('highlight-dropdown');
    if (colorDropdown) colorDropdown.classList.toggle('open');
    if (highlightDropdown) highlightDropdown.classList.remove('open');
}
function setColor(color) {
    if (!editor) return;
    restoreSelection();
    editor.focus();
    document.execCommand('foreColor', false, color);
    const colorDropdown = document.getElementById('color-dropdown');
    if (colorDropdown) colorDropdown.classList.remove('open');
}

// Dropdown de grifar
function toggleHighlightMenu() {
    saveSelection();
    const highlightDropdown = document.getElementById('highlight-dropdown');
    const colorDropdown = document.getElementById('color-dropdown');
    if (highlightDropdown) highlightDropdown.classList.toggle('open');
    if (colorDropdown) colorDropdown.classList.remove('open');
}
function setHighlight(color) {
    if (!editor) return;
    restoreSelection();
    editor.focus();
    document.execCommand('hiliteColor', false, color);
    const highlightDropdown = document.getElementById('highlight-dropdown');
    if (highlightDropdown) highlightDropdown.classList.remove('open');
}

// Fecha menus de cor/grifar ao clicar fora
document.addEventListener('click', function(e) {
    const highlightDropdown = document.getElementById('highlight-dropdown');
    const colorDropdown = document.getElementById('color-dropdown');
    if (highlightDropdown && !highlightDropdown.contains(e.target)) {
        highlightDropdown.classList.remove('open');
    }
    if (colorDropdown && !colorDropdown.contains(e.target)) {
        colorDropdown.classList.remove('open');
    }
});

// Tamanho da fonte
function setFontSize(size) {
    if (!editor) return;
    editor.focus();
    document.execCommand('fontSize', false, size);
}

let savedSelection = null;

function saveSelection() {
    if (window.getSelection) {
        const sel = window.getSelection();
        if (sel.rangeCount > 0) {
            savedSelection = sel.getRangeAt(0);
        }
    }
}

function restoreSelection() {
    if (savedSelection && window.getSelection) {
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(savedSelection);
    }
}
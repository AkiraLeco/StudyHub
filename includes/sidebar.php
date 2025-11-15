<?php
?>
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>Ferramentas</h3>
        <button class="sidebar-toggle" onclick="toggleSidebar()" title="Expandir/Encolher Sidebar">
            <span id="sidebar-toggle-icon">&#9776;</span>
        </button>
    </div>
    <ul>
        <li><button type="button" onclick="toggleToolCard('clock-card')">🕒 Relógio</button></li>
        <li><button type="button" onclick="toggleToolCard('timer-card')">⏱️ Cronômetro</button></li>
        <li><button type="button" onclick="toggleToolCard('motivation-card')">💡 Motivação</button></li>
    </ul>
</aside>

<!-- Cards flutuantes -->
<div id="clock-card" class="tool-card" style="display:none;">
    <div class="tool-card-content" id="clock-card-content">
        <span id="clock"></span>
    </div>
</div>
<div id="timer-card" class="tool-card" style="display:none;">
    <div class="tool-card-content" id="timer-card-content">
        <span id="timer">00:00:00.00</span>
        <div style="margin-top:1rem;">
            <button type="button" onclick="startTimer()">Iniciar</button>
            <button type="button" onclick="pauseTimer()">Pausar</button>
            <button type="button" onclick="resetTimer()">Zerar</button>
        </div>
    </div>
</div>
<div id="motivation-card" class="tool-card" style="display:none;">
    <div class="tool-card-content" id="motivation-card-content">
        <span id="motivation"></span>
        <div style="margin-top:1rem;">
            <button type="button" onclick="showMotivation()">Nova frase</button>
        </div>
    </div>
</div>


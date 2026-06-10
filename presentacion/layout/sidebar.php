<?php
// No mostrar sidebar en login
if (basename($_SERVER['PHP_SELF']) == 'index.php') return;
?>
<button class="menu-toggle" onclick="toggleSidebar()">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
</button>

<div class="overlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
       <img src="../../recursos/logo-cecar.png" alt="Logo">
        <h1>Solicitudes CECAR</h1>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?= basename($_SERVER['SCRIPT_NAME']) == 'dashboard.php' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <?php if ($_SESSION['rol'] === 'solicitante'): ?>
        <a href="solicitudes.php" class="<?= basename($_SERVER['SCRIPT_NAME']) == 'solicitudes.php' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Nueva solicitud
        </a>
        <a href="mis_solicitudes.php" class="<?= basename($_SERVER['SCRIPT_NAME']) == 'mis_solicitudes.php' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Mis solicitudes
        </a>
        <?php elseif ($_SESSION['rol'] === 'revisor'): ?>
        <a href="listar.php" class="<?= basename($_SERVER['SCRIPT_NAME']) == 'listar.php' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Revisar solicitudes
        </a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        <a href="../../controladores/LogoutController.php" class="logout-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Cerrar sesión
        </a>
    </div>
</aside>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.querySelector('.overlay').classList.toggle('show');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.querySelector('.overlay').classList.remove('show');
}
// Cerrar sidebar al hacer clic en enlace (móvil)
document.querySelectorAll('.sidebar-nav a').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 768) closeSidebar();
    });
});
</script>
<style>
.overlay.show { display: block; }
.overlay { display: none; }
@media (max-width: 768px) {
    .sidebar.open { transform: translateX(0); }
}
</style>
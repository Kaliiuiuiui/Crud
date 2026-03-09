<?php
/**
 * Página Principal (index.php)
 * Redireciona para o login ou dashboard dependendo do status da sessão
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se já está logado, redireciona para o dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: /auth_system/dashboard/dashboard.php");
    exit();
}

// Caso contrário, redireciona para o login
header("Location: /auth_system/auth/login.php");
exit();

<?php
/**
 * Script de Logout
 * Encerra a sessão do usuário
 */

declare(strict_types=1);

// Inicia a sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpa todos os dados da sessão
$_SESSION = [];

// Destrói a sessão
session_destroy();

// Redireciona para a página de login
header("Location: /auth_system/auth/login.php");
exit();

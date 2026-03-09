<?php
/**
 * Script de Verificação de Sessão
 * Valida se o usuário está logado e possui as permissões necessárias
 */

declare(strict_types=1);

// Inicia a sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se o usuário está logado
 * Se não estiver, redireciona para a página de login
 */
function verificarLogin(): void {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: /auth_system/auth/login.php");
        exit();
    }
}

/**
 * Verifica se o usuário é administrador
 * Se não for, exibe mensagem de erro e redireciona
 */
function verificarAdmin(): void {
    verificarLogin();
    
    if ($_SESSION['nivel'] !== 'admin') {
        $_SESSION['erro'] = "Acesso negado. Você não tem permissão para acessar esta página.";
        header("Location: /auth_system/dashboard/dashboard.php");
        exit();
    }
}

/**
 * Sanitiza uma string para evitar XSS
 */
function sanitizar(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

/**
 * Valida um email
 */
function validarEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida um formulário POST
 */
function validarCSRF(): bool {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    return true;
}

/**
 * Gera um token CSRF
 */
function gerarCSRFToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

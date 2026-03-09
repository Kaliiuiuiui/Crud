<?php
/**
 * Script de Deleção de Usuário
 * Exclusivo para administradores
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../auth/verificar_sessao.php';

verificarAdmin();

$conexao = Conexao::getInstance();
$usuario_id = (int)($_GET['id'] ?? 0);

if ($usuario_id <= 0) {
    $_SESSION['erro'] = "ID de usuário inválido.";
    header("Location: /auth_system/usuarios/listar.php");
    exit();
}

// Verifica se o usuário existe
$usuario = $conexao->buscarUm(
    "SELECT id FROM usuarios WHERE id = ?",
    [$usuario_id]
);

if (!$usuario) {
    $_SESSION['erro'] = "Usuário não encontrado.";
    header("Location: /auth_system/usuarios/listar.php");
    exit();
}

// Impede que o admin delete a si mesmo
if ($usuario_id === (int)$_SESSION['usuario_id']) {
    $_SESSION['erro'] = "Você não pode deletar sua própria conta.";
    header("Location: /auth_system/usuarios/listar.php");
    exit();
}

try {
    // Deleta o usuário
    $conexao->deletar(
        "DELETE FROM usuarios WHERE id = ?",
        [$usuario_id]
    );
    $_SESSION['sucesso'] = "Usuário deletado com sucesso!";
} catch (Exception $e) {
    $_SESSION['erro'] = "Erro ao deletar usuário: " . $e->getMessage();
}

header("Location: /auth_system/usuarios/listar.php");
exit();

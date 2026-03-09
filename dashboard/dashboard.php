<?php
/**
 * Dashboard Principal
 * Página inicial após login
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../auth/verificar_sessao.php';

verificarLogin();

$nome = sanitizar($_SESSION['nome']);
$email = sanitizar($_SESSION['email']);
$nivel = sanitizar($_SESSION['nivel']);
$isAdmin = ($_SESSION['nivel'] === 'admin');
$erro = $_SESSION['erro'] ?? '';
$sucesso = $_SESSION['sucesso'] ?? '';

// Limpa as mensagens da sessão
unset($_SESSION['erro'], $_SESSION['sucesso']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Autenticação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/auth_system/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/auth_system/dashboard/dashboard.php">
                <i class="bi bi-shield-lock"></i> Sistema de Autenticação
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/auth_system/usuarios/listar.php">Gerenciar Usuários</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo $nome; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/auth_system/dashboard/perfil.php">Meu Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/auth_system/auth/logout.php">Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo sanitizar($erro); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo sanitizar($sucesso); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <h1 class="mb-4">Bem-vindo, <?php echo $nome; ?>!</h1>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informações da Conta</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Nome:</strong>
                                <p><?php echo $nome; ?></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong>
                                <p><?php echo $email; ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nível de Acesso:</strong>
                                <p>
                                    <?php if ($isAdmin): ?>
                                        <span class="badge bg-danger">Administrador</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Usuário</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($isAdmin): ?>
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Funções de Administrador</h5>
                        </div>
                        <div class="card-body">
                            <p>Como administrador, você tem acesso às seguintes funções:</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <a href="/auth_system/usuarios/listar.php" class="text-decoration-none">
                                        <strong>Gerenciar Usuários</strong> - Visualizar, criar, editar e deletar usuários
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="/auth_system/usuarios/criar.php" class="text-decoration-none">
                                        <strong>Criar Novo Usuário</strong> - Adicionar um novo usuário ao sistema
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Informações do Usuário</h5>
                        </div>
                        <div class="card-body">
                            <p>Você está logado como usuário comum. Você pode:</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <a href="/auth_system/dashboard/perfil.php" class="text-decoration-none">
                                        <strong>Visualizar seu Perfil</strong> - Ver e atualizar suas informações
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Ações Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <a href="/auth_system/dashboard/perfil.php" class="btn btn-outline-primary w-100 mb-2">
                            Meu Perfil
                        </a>
                        <a href="/auth_system/auth/logout.php" class="btn btn-outline-danger w-100">
                            Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-4 mt-5">
        <div class="container">
            <p>&copy; 2026 Sistema de Autenticação. Desenvolvido com segurança em mente.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
/**
 * Página de Criação de Usuário
 * Exclusivo para administradores
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../auth/verificar_sessao.php';

verificarAdmin();

$conexao = Conexao::getInstance();
$erro = '';
$sucesso = '';

// Processa a criação de novo usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCSRF()) {
        $erro = "Token de segurança inválido.";
    } else {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = $_POST['senha'] ?? '';
        $nivel = $_POST['nivel'] ?? 'user';

        if (empty($nome)) {
            $erro = "Nome é obrigatório.";
        } elseif (!$email) {
            $erro = "Email inválido.";
        } elseif (strlen($senha) < 6) {
            $erro = "Senha deve ter pelo menos 6 caracteres.";
        } elseif (!in_array($nivel, ['admin', 'user'])) {
            $erro = "Nível de acesso inválido.";
        } else {
            try {
                // Verifica se o email já existe
                $usuarioExistente = $conexao->buscarUm(
                    "SELECT id FROM usuarios WHERE email = ?",
                    [$email]
                );

                if ($usuarioExistente) {
                    $erro = "Este email já está registrado.";
                } else {
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    $conexao->inserir(
                        "INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, ?)",
                        [$nome, $email, $senha_hash, $nivel]
                    );
                    $_SESSION['sucesso'] = "Usuário criado com sucesso!";
                    header("Location: /auth_system/usuarios/listar.php");
                    exit();
                }
            } catch (Exception $e) {
                $erro = "Erro ao criar usuário: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Usuário - Sistema de Autenticação</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="/auth_system/dashboard/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth_system/usuarios/listar.php">Usuários</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/auth_system/auth/logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h1 class="mb-4">Criar Novo Usuário</h1>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo sanitizar($erro); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informações do Novo Usuário</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo gerarCSRFToken(); ?>">

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                                <small class="form-text text-muted">Mínimo 6 caracteres</small>
                            </div>

                            <div class="mb-3">
                                <label for="nivel" class="form-label">Nível de Acesso</label>
                                <select class="form-select" id="nivel" name="nivel" required>
                                    <option value="user">Usuário Comum</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="/auth_system/usuarios/listar.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Criar Usuário</button>
                            </div>
                        </form>
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

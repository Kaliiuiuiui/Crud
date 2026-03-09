<?php
/**
 * Página de Perfil do Usuário
 * Permite visualizar e editar informações pessoais
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../auth/verificar_sessao.php';

verificarLogin();

$conexao = Conexao::getInstance();
$usuario_id = (int)$_SESSION['usuario_id'];
$erro = '';
$sucesso = '';

// Busca os dados do usuário
$usuario = $conexao->buscarUm(
    "SELECT id, nome, email, nivel FROM usuarios WHERE id = ?",
    [$usuario_id]
);

if (!$usuario) {
    $_SESSION['erro'] = "Usuário não encontrado.";
    header("Location: /auth_system/dashboard/dashboard.php");
    exit();
}

// Processa atualização de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCSRF()) {
        $erro = "Token de segurança inválido.";
    } else {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $senha_atual = $_POST['senha_atual'] ?? '';
        $senha_nova = $_POST['senha_nova'] ?? '';
        $senha_confirma = $_POST['senha_confirma'] ?? '';

        if (empty($nome)) {
            $erro = "Nome é obrigatório.";
        } else {
            try {
                // Se quer alterar a senha
                if (!empty($senha_nova)) {
                    // Verifica a senha atual
                    $usuarioDb = $conexao->buscarUm(
                        "SELECT senha FROM usuarios WHERE id = ?",
                        [$usuario_id]
                    );

                    if (!password_verify($senha_atual, $usuarioDb['senha'])) {
                        $erro = "Senha atual incorreta.";
                    } elseif ($senha_nova !== $senha_confirma) {
                        $erro = "As senhas não coincidem.";
                    } elseif (strlen($senha_nova) < 6) {
                        $erro = "A nova senha deve ter pelo menos 6 caracteres.";
                    } else {
                        $senha_hash = password_hash($senha_nova, PASSWORD_DEFAULT);
                        $conexao->atualizar(
                            "UPDATE usuarios SET nome = ?, senha = ? WHERE id = ?",
                            [$nome, $senha_hash, $usuario_id]
                        );
                        $sucesso = "Perfil e senha atualizados com sucesso!";
                        $_SESSION['nome'] = $nome;
                        $usuario['nome'] = $nome;
                    }
                } else {
                    // Apenas atualiza o nome
                    $conexao->atualizar(
                        "UPDATE usuarios SET nome = ? WHERE id = ?",
                        [$nome, $usuario_id]
                    );
                    $sucesso = "Perfil atualizado com sucesso!";
                    $_SESSION['nome'] = $nome;
                    $usuario['nome'] = $nome;
                }
            } catch (Exception $e) {
                $erro = "Erro ao atualizar perfil: " . $e->getMessage();
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
    <title>Meu Perfil - Sistema de Autenticação</title>
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
                        <a class="nav-link active" href="/auth_system/dashboard/perfil.php">Meu Perfil</a>
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
                <h1 class="mb-4">Meu Perfil</h1>

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

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informações Pessoais</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo gerarCSRFToken(); ?>">

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo sanitizar($usuario['nome']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email (não pode ser alterado)</label>
                                <input type="email" class="form-control" id="email" value="<?php echo sanitizar($usuario['email']); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="nivel" class="form-label">Nível de Acesso</label>
                                <input type="text" class="form-control" id="nivel" value="<?php echo ucfirst(sanitizar($usuario['nivel'])); ?>" disabled>
                            </div>

                            <hr>

                            <h5 class="mb-3">Alterar Senha</h5>

                            <div class="mb-3">
                                <label for="senha_atual" class="form-label">Senha Atual</label>
                                <input type="password" class="form-control" id="senha_atual" name="senha_atual" placeholder="Deixe em branco para não alterar">
                            </div>

                            <div class="mb-3">
                                <label for="senha_nova" class="form-label">Nova Senha</label>
                                <input type="password" class="form-control" id="senha_nova" name="senha_nova" placeholder="Deixe em branco para não alterar">
                            </div>

                            <div class="mb-3">
                                <label for="senha_confirma" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="senha_confirma" name="senha_confirma" placeholder="Deixe em branco para não alterar">
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="/auth_system/dashboard/dashboard.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
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

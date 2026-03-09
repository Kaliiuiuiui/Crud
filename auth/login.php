<?php
/**
 * Página de Login
 * Autentica o usuário e inicia a sessão
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/verificar_sessao.php';

// Se já está logado, redireciona para o dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: /auth_system/dashboard/dashboard.php");
    exit();
}

$erro = '';
$sucesso = '';

// Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$email || empty($senha)) {
        $erro = "Email e senha são obrigatórios.";
    } else {
        try {
            $conexao = Conexao::getInstance();
            $usuario = $conexao->buscarUm(
                "SELECT id, nome, email, senha, nivel FROM usuarios WHERE email = ?",
                [$email]
            );

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Regenera o ID da sessão para evitar Session Fixation
                session_regenerate_id(true);

                // Armazena dados do usuário na sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['nivel'] = $usuario['nivel'];

                $sucesso = "Login realizado com sucesso!";
                header("Refresh: 1; url=/auth_system/dashboard/dashboard.php");
            } else {
                $erro = "Email ou senha inválidos.";
            }
        } catch (Exception $e) {
            $erro = "Erro ao processar login: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Autenticação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/auth_system/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-container {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #333;
            font-weight: 700;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        .credentials-info {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 1rem;
            border-radius: 6px;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .credentials-info strong {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .credentials-info p {
            margin: 0.25rem 0;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Sistema de Autenticação</h2>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Erro!</strong> <?php echo sanitizar($erro); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sucesso!</strong> <?php echo sanitizar($sucesso); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>

            <button type="submit" class="btn btn-login btn-primary w-100 text-white">Entrar</button>
        </form>

        <div class="credentials-info">
            <strong>Credenciais de Teste:</strong>
            <p><strong>Admin:</strong> admin@email.com / admin123</p>
            <p><strong>Usuário:</strong> user@email.com / user123</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

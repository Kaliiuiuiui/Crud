<?php
/**
 * Script de Teste de Conexão
 * Verifica se a conexão com o banco de dados está funcionando
 */

declare(strict_types=1);

require_once __DIR__ . '/config/conexao.php';

$status = 'error';
$mensagem = '';

try {
    $conexao = Conexao::getInstance();
    $pdo = $conexao->getPDO();
    
    // Testa a conexão
    $resultado = $pdo->query("SELECT 1");
    
    if ($resultado) {
        $status = 'success';
        $mensagem = 'Conexão com o banco de dados estabelecida com sucesso!';
        
        // Busca informações do banco
        $usuarios = $conexao->buscarTodos("SELECT COUNT(*) as total FROM usuarios");
        $totalUsuarios = $usuarios[0]['total'] ?? 0;
    } else {
        $mensagem = 'Erro ao testar a conexão.';
    }
} catch (Exception $e) {
    $mensagem = 'Erro: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Conexão - Sistema de Autenticação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container-test {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container-test">
        <h2 class="text-center mb-4">Teste de Conexão</h2>
        
        <?php if ($status === 'success'): ?>
            <div class="alert alert-success" role="alert">
                <strong>Sucesso!</strong> <?php echo htmlspecialchars($mensagem); ?>
            </div>
            <div class="card">
                <div class="card-body">
                    <p><strong>Total de Usuários:</strong> <?php echo $totalUsuarios; ?></p>
                    <p><strong>Banco de Dados:</strong> auth_system</p>
                    <p><strong>Status:</strong> <span class="badge bg-success">Operacional</span></p>
                </div>
            </div>
            <div class="mt-3 text-center">
                <a href="/auth_system/auth/login.php" class="btn btn-primary">Ir para Login</a>
            </div>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                <strong>Erro!</strong> <?php echo htmlspecialchars($mensagem); ?>
            </div>
            <div class="card">
                <div class="card-body">
                    <h6>Verifique:</h6>
                    <ul>
                        <li>Se o MySQL está rodando</li>
                        <li>Se o banco de dados 'auth_system' foi criado</li>
                        <li>Se as credenciais em config/conexao.php estão corretas</li>
                        <li>Se o usuário 'app_user' foi criado com as permissões necessárias</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

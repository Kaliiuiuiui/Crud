<?php
/**
 * Classe de Conexão com Banco de Dados
 * Utiliza PDO para segurança contra SQL Injection
 * Suporta UTF8MB4 para caracteres especiais
 */

declare(strict_types=1);

class Conexao {
    private static ?Conexao $instance = null;
    private PDO $pdo;

    /**
     * Construtor privado para implementar padrão Singleton
     */
    private function __construct() {
        $host = 'localhost';
        $db_name = 'auth_system';
        $username = 'app_user';
        $password = 'app_password';

        try {
            $this->pdo = new PDO(
                "mysql:host=$host;dbname=$db_name;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => false
                ]
            );
        } catch (PDOException $e) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Obtém instância única da conexão (Singleton)
     */
    public static function getInstance(): Conexao {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retorna a instância do PDO
     */
    public function getPDO(): PDO {
        return $this->pdo;
    }

    /**
     * Executa uma query preparada
     */
    public function executar(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Busca um único registro
     */
    public function buscarUm(string $sql, array $params = []): ?array {
        $stmt = $this->executar($sql, $params);
        return $stmt->fetch() ?: null;
    }

    /**
     * Busca múltiplos registros
     */
    public function buscarTodos(string $sql, array $params = []): array {
        $stmt = $this->executar($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insere um novo registro
     */
    public function inserir(string $sql, array $params = []): int {
        $this->executar($sql, $params);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Atualiza um registro
     */
    public function atualizar(string $sql, array $params = []): int {
        $stmt = $this->executar($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Deleta um registro
     */
    public function deletar(string $sql, array $params = []): int {
        $stmt = $this->executar($sql, $params);
        return $stmt->rowCount();
    }
}

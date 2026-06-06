<?php
declare(strict_types=1);

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'bancologin');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'Luc_4s111');

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        throw new RuntimeException('Nao foi possivel conectar ao banco de dados. Verifique se o MySQL/MariaDB do WAMP esta ligado, se o banco "' . DB_NAME . '" existe na porta ' . DB_PORT . ' e se usuario/senha estao corretos.', 0, $e);
    }

    return $pdo;
}

function read_request_data(): array
{
    $raw = file_get_contents('php://input');
    $json = json_decode($raw ?: '', true);

    if (is_array($json)) {
        return $json;
    }

    return $_POST;
}

function respond_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function password_is_bcrypt(string $hash): bool
{
    return strncmp($hash, '$2y$', 4) === 0 || strncmp($hash, '$2b$', 4) === 0 || strncmp($hash, '$2a$', 4) === 0;
}

function verify_password_and_migrate(string $senha, string $senhaBanco, PDO $pdo, string $tabela, int $id): bool
{
    if (password_is_bcrypt($senhaBanco)) {
        return password_verify($senha, $senhaBanco);
    }

    if (hash_equals($senhaBanco, $senha)) {
        $hash = password_hash($senha, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE {$tabela} SET senha = ? WHERE id = ?");
        $stmt->execute([$hash, $id]);
        return true;
    }

    return false;
}
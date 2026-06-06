<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond_json(['sucesso' => false, 'mensagem' => 'Metodo nao suportado.'], 405);
}

$body = read_request_data();
$login = trim((string)($body['login'] ?? ''));
$senha = (string)($body['senha'] ?? '');

if ($login === '' || $senha === '') {
    respond_json(['sucesso' => false, 'mensagem' => 'Informe login e senha.'], 400);
}

try {
    $pdo = db();

    $stmt = $pdo->prepare('SELECT id, nome, login, senha FROM usuario WHERE login = ? LIMIT 1');
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && verify_password_and_migrate($senha, (string)$user['senha'], $pdo, 'usuario', (int)$user['id'])) {
        respond_json([
            'sucesso' => true,
            'tipo' => 'usuario',
            'usuario' => [
                'id' => (int)$user['id'],
                'nome' => $user['nome'],
                'login' => $user['login'],
            ],
        ]);
    }

    $stmt = $pdo->prepare('SELECT id, nome, login, senha FROM admin WHERE login = ? LIMIT 1');
    $stmt->execute([$login]);
    $admin = $stmt->fetch();

    if ($admin && verify_password_and_migrate($senha, (string)$admin['senha'], $pdo, 'admin', (int)$admin['id'])) {
        respond_json([
            'sucesso' => true,
            'tipo' => 'admin',
            'admin' => [
                'id' => (int)$admin['id'],
                'nome' => $admin['nome'],
                'login' => $admin['login'],
            ],
        ]);
    }

    respond_json(['sucesso' => false, 'mensagem' => 'Login ou senha invalidos.'], 401);
} catch (Throwable $e) {
    respond_json(['sucesso' => false, 'mensagem' => $e->getMessage()], 500);
}

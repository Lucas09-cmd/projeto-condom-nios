<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond_json(['sucesso' => false, 'mensagem' => 'Metodo nao suportado.'], 405);
}

$body = read_request_data();
$nome = trim((string)($body['nome'] ?? ''));
$login = trim((string)($body['login'] ?? ''));
$senha = (string)($body['senha'] ?? '');

if ($nome === '' || $login === '' || $senha === '') {
    respond_json(['sucesso' => false, 'mensagem' => 'Preencha nome, e-mail e senha.'], 400);
}

if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
    respond_json(['sucesso' => false, 'mensagem' => 'Informe um e-mail valido.'], 400);
}

if (strlen($senha) < 8) {
    respond_json(['sucesso' => false, 'mensagem' => 'A senha deve conter pelo menos 8 caracteres.'], 400);
}

try {
    $pdo = db();

    $check = $pdo->prepare('SELECT id FROM usuario WHERE login = ? LIMIT 1');
    $check->execute([$login]);

    if ($check->fetch()) {
        respond_json(['sucesso' => false, 'mensagem' => 'Este e-mail ja esta cadastrado.'], 409);
    }

    $hash = password_hash($senha, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO usuario (nome, login, senha) VALUES (?, ?, ?)');
    $stmt->execute([$nome, $login, $hash]);

    respond_json(['sucesso' => true, 'mensagem' => 'Usuario cadastrado com sucesso!']);
} catch (Throwable $e) {
    respond_json(['sucesso' => false, 'mensagem' => $e->getMessage()], 500);
}

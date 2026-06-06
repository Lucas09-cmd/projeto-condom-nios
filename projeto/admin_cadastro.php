<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// mostra erro no WAMP/XAMPP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config.php';

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Método não permitido.'
        ]);

        exit;
    }

    $json = file_get_contents('php://input');

    $body = json_decode($json, true);

    if (!$body) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'JSON inválido.'
        ]);

        exit;
    }

    $nome = trim($body['nome'] ?? '');
    $login = trim($body['login'] ?? '');
    $senha = $body['senha'] ?? '';

    if (
        empty($nome) ||
        empty($login) ||
        empty($senha)
    ) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Preencha todos os campos.'
        ]);

        exit;
    }

    if (!filter_var(
        $login,
        FILTER_VALIDATE_EMAIL
    )) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail inválido.'
        ]);

        exit;
    }

    if (strlen($senha) < 8) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' =>
            'A senha deve ter no mínimo 8 caracteres.'
        ]);

        exit;
    }

    $pdo = db();

    $check = $pdo->prepare(
        "SELECT id
         FROM admin
         WHERE login = ?
         LIMIT 1"
    );

    $check->execute([$login]);

    if ($check->fetch()) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' =>
            'Este e-mail já está cadastrado.'
        ]);

        exit;
    }

    $hash = password_hash(
        $senha,
        PASSWORD_DEFAULT
    );

    $stmt = $pdo->prepare(
        "INSERT INTO admin
        (nome, login, senha)
        VALUES (?, ?, ?)"
    );

    $stmt->execute([
        $nome,
        $login,
        $hash
    ]);

    echo json_encode([
        'sucesso' => true,
        'mensagem' =>
        'Administrador cadastrado com sucesso.'
    ]);

} catch (Throwable $e) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' =>
        $e->getMessage()
    ]);
}

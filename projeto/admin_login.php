<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config.php';

try {

    // aceita apenas POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Método não permitido.'
        ]);

        exit;
    }

    // pega JSON enviado
    $body = json_decode(
        file_get_contents('php://input'),
        true
    );

    $login = trim(
        $body['login'] ?? ''
    );

    $senha =
        $body['senha'] ?? '';

    // validação
    if (
        empty($login) ||
        empty($senha)
    ) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' =>
            'Informe login e senha.'
        ]);

        exit;
    }

    $pdo = db();

    // busca admin
    $stmt = $pdo->prepare(
        "SELECT
            id,
            nome,
            login,
            senha
        FROM admin
        WHERE login = ?
        LIMIT 1"
    );

    $stmt->execute([
        $login
    ]);

    $admin =
        $stmt->fetch();

    // admin não encontrado
    if (!$admin) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' =>
            'Login ou senha inválidos.'
        ]);

        exit;
    }

    // verifica senha
    if (
        !password_verify(
            $senha,
            $admin['senha']
        )
    ) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' =>
            'Login ou senha inválidos.'
        ]);

        exit;
    }

    // sucesso
    echo json_encode([
        'sucesso' => true,

        'admin' => [
            'id' =>
                (int)$admin['id'],

            'nome' =>
                $admin['nome'],

            'login' =>
                $admin['login']
        ]
    ]);

} catch (Throwable $e) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' =>
            $e->getMessage()
    ]);
}
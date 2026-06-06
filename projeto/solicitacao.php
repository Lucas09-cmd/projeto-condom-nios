<?php
declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config.php';

function read_json(): array
{
    $raw = file_get_contents('php://input');

    $data = json_decode(
        $raw ?: '',
        true
    );

    return is_array($data)
        ? $data
        : [];
}

function respond(
    array $payload,
    int $status = 200
): void {

    http_response_code(
        $status
    );

    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

$method =
$_SERVER['REQUEST_METHOD'];


// =====================================
// GET
// LISTAR SOLICITAÇÕES
// =====================================

if ($method === 'GET') {

    $id_usuario =
    (int)(
        $_GET['id_usuario']
        ?? 0
    );

    if ($id_usuario <= 0) {

        respond([
            'sucesso' => false,
            'mensagem' =>
            'Usuário inválido.'
        ], 400);
    }

    try {

        $pdo = db();

        $stmt =
        $pdo->prepare(
            "SELECT
                id,
                descricao,
                valor,
                data,
                status
            FROM solicitacao
            WHERE id_usuario = ?
            ORDER BY data DESC"
        );

        $stmt->execute([
            $id_usuario
        ]);

        respond([
            'sucesso' => true,
            'solicitacoes' =>
            $stmt->fetchAll()
        ]);

    } catch (
        Throwable $e
    ) {

        respond([
            'sucesso' => false,
            'mensagem' =>
            $e->getMessage()
        ], 500);
    }
}


// =====================================
// POST
// CRIAR SOLICITAÇÃO
// =====================================

if ($method === 'POST') {

    try {

        $body =
        read_json();

        $id_usuario =
        (int)(
            $body[
                'id_usuario'
            ] ?? 0
        );

        $descricao =
        trim(
            $body[
                'descricao'
            ] ?? ''
        );

        $valor =
        isset(
            $body['valor']
        )
        &&
        $body['valor'] !== ''
        ? (float)$body['valor']
        : null;


        // validação
        if (
            $id_usuario <= 0
        ) {

            respond([
                'sucesso' => false,
                'mensagem' =>
                'Usuário inválido.'
            ], 400);
        }

        // exige descrição OU valor
        if (
            $descricao === ''
            &&
            $valor === null
        ) {

            respond([
                'sucesso' => false,
                'mensagem' =>
                'Digite uma descrição ou valor.'
            ], 400);
        }

        $pdo = db();

        // verifica usuário
        $check =
        $pdo->prepare(
            "SELECT id
            FROM usuario
            WHERE id = ?
            LIMIT 1"
        );

        $check->execute([
            $id_usuario
        ]);

        if (
            !$check->fetch()
        ) {

            respond([
                'sucesso' => false,
                'mensagem' =>
                'Usuário não encontrado.'
            ], 404);
        }

        // INSERT
        $stmt =
        $pdo->prepare(
            "INSERT INTO
            solicitacao
            (
                id_usuario,
                descricao,
                valor,
                data,
                status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                NOW(),
                'pendente'
            )"
        );

        $stmt->execute([

            $id_usuario,

            $descricao,

            $valor
        ]);

        respond([

            'sucesso' => true,

            'mensagem' =>
            'Solicitação criada.',

            'solicitacao' => [

                'id' =>
                $pdo->lastInsertId(),

                'descricao' =>
                $descricao,

                'valor' =>
                $valor,

                'status' =>
                'pendente',

                'data' =>
                date(
                    'Y-m-d H:i:s'
                )
            ]
        ]);

    } catch (
        Throwable $e
    ) {

        respond([
            'sucesso' => false,
            'mensagem' =>
            $e->getMessage()
        ], 500);
    }
}

respond([
    'sucesso' => false,
    'mensagem' =>
    'Método não permitido.'
], 405);
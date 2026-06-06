<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config.php';

try {
    $pdo = db();
} catch (Throwable $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro na conexão: ' . $e->getMessage()
    ]);
    exit;
}


// ========================================
// POST → Atualizar status
// ========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $body = json_decode(
            file_get_contents('php://input'),
            true
        );

        $id = (int)(
            $body['id']
            ?? 0
        );

        $status = trim(
            $body['status']
            ?? ''
        );

        if (
            $id <= 0
            || !$status
        ) {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Dados inválidos.'
            ]);

            exit;
        }

        // somente status válidos
        $permitidos = [
            'pendente',
            'aprovado',
            'rejeitado'
        ];

        if (
            !in_array(
                $status,
                $permitidos,
                true
            )
        ) {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Status inválido.'
            ]);

            exit;
        }

        $stmt = $pdo->prepare(
            "UPDATE solicitacao
             SET status = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $status,
            $id
        ]);

        echo json_encode([
            'sucesso' => true
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' =>
            'Erro ao atualizar: '
            . $e->getMessage()
        ]);
    }

    exit;
}


// ========================================
// GET → Listar solicitações
// ========================================

try {

    $stmt = $pdo->query("
        SELECT
            s.id,
            s.descricao,
            s.valor,
            s.status,
            s.data,

            u.nome
            AS nome_usuario,

            u.login
            AS login_usuario

        FROM solicitacao s

        LEFT JOIN usuario u
        ON s.id_usuario = u.id

        ORDER BY s.data DESC
    ");

    $solicitacoes =
    $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );

    echo json_encode([
        'sucesso' => true,
        'solicitacoes' =>
        $solicitacoes
    ]);

} catch (Throwable $e) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' =>
        'Erro SQL: '
        . $e->getMessage()
    ]);
}
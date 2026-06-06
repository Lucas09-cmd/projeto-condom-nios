<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

$pdo = db();

$idUsuario = (int)($_GET['id_usuario'] ?? 0);

if (!$idUsuario) {
    respond_json([
        'sucesso' => false,
        'mensagem' => 'Usuário inválido'
    ], 400);
}

$stmt = $pdo->prepare("
    SELECT
        descricao,
        status,
        data
    FROM solicitacao
    WHERE id_usuario = ?
    ORDER BY data DESC
");

$stmt->execute([$idUsuario]);

respond_json([
    'sucesso' => true,
    'solicitacoes' => $stmt->fetchAll()
]);
<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

try {

    $pdo = db();

    $banco = $pdo
        ->query("SELECT DATABASE()")
        ->fetchColumn();

    echo "<h2>Conexão OK</h2>";

    echo "<strong>Banco atual:</strong> "
        . $banco
        . "<br>";

    echo "<strong>Porta:</strong> "
        . DB_PORT
        . "<br><br>";

    echo "<h3>Tabela admin:</h3>";

    $stmt = $pdo->query(
        "SELECT * FROM admin"
    );

    $admins =
        $stmt->fetchAll();

    echo "<pre>";
    print_r($admins);
    echo "</pre>";

} catch (Throwable $e) {

    echo "<h3>ERRO:</h3>";
    echo $e->getMessage();
}

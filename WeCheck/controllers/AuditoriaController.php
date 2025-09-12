<?php
    require __DIR__ . "/../config/db.php";
    $pdo = Database::getConnection();

    $idUsuario = $_SESSION['id_usuario'];

    // Buscar auditorias do usuário logado
    $stmt = $pdo->prepare("
        SELECT id_auditoria, nome_auditoria, empresa_auditoria, documento_pdf 
        FROM tb_auditoria 
        WHERE id_usuario = ? 
        ORDER BY id_auditoria DESC
    ");
    $stmt->execute([$idUsuario]);
    $auditorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
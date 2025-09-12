<?php
require __DIR__ . "/../config/db.php";
    $pdo = Database::getConnection();

    $idUsuario = $_SESSION['id_usuario'];


class AuditoriaCriacaoController {
    public static function criarAuditoria($idUsuario, $nomeAuditoria, $empresaAuditoria, $documentoPdf) {
        require __DIR__ . "/../config/db.php";
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("INSERT INTO tb_auditoria 
            (id_usuario, nome_auditoria, empresa_auditoria, documento_pdf, data_criacao) 
            VALUES (?, ?, ?, ?, NOW())");

        if ($stmt->execute([$idUsuario, $nomeAuditoria, $empresaAuditoria, $documentoPdf])) {
            session_start();
            $_SESSION['id_auditoria'] = $pdo->lastInsertId();
            header('Location: ../views/checklist_view.php');
            exit;
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Erro ao cadastrar: " . $errorInfo[2];
        }
    }
}
?>
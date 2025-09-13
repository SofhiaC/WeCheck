<?php
class ListarAuditoriaController {
    public static function listarAuditorias() {
        $pdo = Database::getConnection();
        $idUsuario = $_SESSION['id_usuario'];

        $stmt = $pdo->prepare("
            SELECT id_auditoria, nome_auditoria, empresa_auditoria, documento_pdf, data_criacao 
            FROM tb_auditoria 
            WHERE id_usuario = ? 
            ORDER BY data_criacao DESC
        ");
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //pega uma auditoria pelo ID
    public static function pegarAuditoria($idAuditoria) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT id_auditoria, nome_auditoria, empresa_auditoria, documento_pdf, data_criacao
            FROM tb_auditoria
            WHERE id_auditoria = ?
        ");
        $stmt->execute([$idAuditoria]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // retorna null se não encontrar
    }
}

$auditorias = ListarAuditoriaController::listarAuditorias();
?>
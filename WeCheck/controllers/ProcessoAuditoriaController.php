<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/ChecklistController.php';
require_once __DIR__ . '/ListarAuditoriaController.php';

class ProcessoAuditoriaController {

    public static function pegarAuditoria($idAuditoria) {
        return ListarAuditoriaController::pegarAuditoria($idAuditoria);
    }

    public static function listarItensAuditoria($idAuditoria) {
        return ChecklistController::listarItens($idAuditoria);
    }

    public static function contarItens($idAuditoria) {
        $itens = self::listarItensAuditoria($idAuditoria);
        return count($itens);
    }

    public static function atualizarResultado($idItem, $resultado) {
    $db = Database::getConnection();

    $sql = "UPDATE tb_checklist SET resultado_item = ? WHERE id_item = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$resultado, $idItem]);

    return ['success' => true];
}
}
?>

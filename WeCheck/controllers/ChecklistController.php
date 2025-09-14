<?php

require_once __DIR__ . '/../config/db.php'; // conexão PDO ($db) 
class ChecklistController {

    public static function adicionarItem($idAuditoria, $nomeItem)
    {
        $db = Database::getConnection(); // pega a conexão PDO correta

        try {
            $sql = "SELECT COALESCE(MAX(ordem_item), 0) + 1 AS proxima_ordem 
                    FROM tb_checklist 
                    WHERE id_auditoria = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$idAuditoria]);
            $proximaOrdem = $stmt->fetchColumn();

            $sql = "INSERT INTO tb_checklist (id_auditoria, nome_item, ordem_item) 
                    VALUES (?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$idAuditoria, $nomeItem, $proximaOrdem]);

            return [
                'success' => true,
                'message' => 'Item adicionado com sucesso!',
                'ordem_item' => $proximaOrdem
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro ao adicionar item: ' . $e->getMessage()
            ];
        }
    }

    public static function listarItens($idAuditoria) {
        $db = Database::getConnection();
        $sql = "SELECT id_item, nome_item, ordem_item, resultado_item 
                FROM tb_checklist 
                WHERE id_auditoria = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$idAuditoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function atualizarResultado($idItem, $resultado) {
    $db = Database::getConnection();

    var_dump($idItem, $resultado);
    exit;

    $sql = "UPDATE tb_checklist SET resultado_item = ? WHERE id_item = ?";
    $stmt = $db->prepare($sql);
    return $stmt->execute([$resultado, $idItem]);
    }
}
?>
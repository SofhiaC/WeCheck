<?php
require_once __DIR__ . '/../config/db.php';

class SetorController {
    public static function adicionarSetor($idAuditoria, $nomeSetor, $gerente, $emailGerente) {
        $db = Database::getConnection();

        try {
            $sql = "INSERT INTO tb_setor (nome_setor, gerente_responsavel, email_gerente, id_auditoria)
                    VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$nomeSetor, $gerente, $emailGerente, $idAuditoria]);

            return [
                'success' => true,
                'message' => 'Setor cadastrado com sucesso!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro ao cadastrar setor: ' . $e->getMessage()
            ];
        }
    }

    public static function listarSetores($idAuditoria) {
        $db = Database::getConnection();
        $sql = "SELECT id_setor, nome_setor, gerente_responsavel, email_gerente 
                FROM tb_setor 
                WHERE id_auditoria = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$idAuditoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
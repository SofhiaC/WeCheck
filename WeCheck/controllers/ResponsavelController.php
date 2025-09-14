<?php
require_once __DIR__ . '/../config/db.php';

class ResponsavelController {
    public static function adicionarResponsavel($idAuditoria, $nome, $email, $cargo) {
        $db = Database::getConnection();

        try {
            $sql = "INSERT INTO tb_responsavel (id_auditoria, nome_responsavel, email_responsavel, cargo_responsavel)
                    VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$idAuditoria, $nome, $email, $cargo]);

            return [
                'success' => true,
                'message' => 'Responsável adicionado com sucesso!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro ao adicionar responsável: ' . $e->getMessage()
            ];
        }
    }

    public static function listarResponsaveis($idAuditoria) {
        $db = Database::getConnection();

        $sql = "SELECT id_responsavel, nome_responsavel, email_responsavel, cargo_responsavel
                FROM tb_responsavel
                WHERE id_setor = ?
                ORDER BY id_responsavel DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$idAuditoria]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

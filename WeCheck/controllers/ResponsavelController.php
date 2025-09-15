<?php
require_once __DIR__ . '/../config/db.php';

class ResponsavelController {
    public static function adicionarResponsavel($idAuditoria, $nome, $email, $cargo, $idSetor) {
        $db = Database::getConnection();

        try {
            $sql = "INSERT INTO tb_responsavel (nome_responsavel, email_responsavel, cargo_responsavel, id_auditoria, id_setor)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$nome, $email, $cargo, $idAuditoria, $idSetor]);

            return ['success' => true, 'message' => 'Responsável cadastrado com sucesso!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao cadastrar responsável: ' . $e->getMessage()];
        }
    }

    public static function listarResponsaveis($idAuditoria) {
        $db = Database::getConnection();

        $sql = "SELECT r.id_responsavel,
                       r.nome_responsavel,
                       r.email_responsavel,
                       r.cargo_responsavel,
                       s.nome_setor
                FROM tb_responsavel r
                LEFT JOIN tb_setor s ON r.id_setor = s.id_setor
                WHERE r.id_auditoria = ?
                ORDER BY s.nome_setor, r.nome_responsavel";

        $stmt = $db->prepare($sql);
        $stmt->execute([$idAuditoria]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
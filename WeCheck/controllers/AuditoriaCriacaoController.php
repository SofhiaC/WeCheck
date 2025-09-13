<?php
class AuditoriaCriacaoController {
    public static function criarAuditoria($idUsuario, $nomeAuditoria, $empresaAuditoria, $documentoPdf) {
        $pdo = Database::getConnection(); // Conexão única

        try {
            $stmt = $pdo->prepare("INSERT INTO tb_auditoria 
                (id_usuario, nome_auditoria, empresa_auditoria, documento_pdf, data_criacao) 
                VALUES (?, ?, ?, ?, NOW())");

            if ($stmt->execute([$idUsuario, $nomeAuditoria, $empresaAuditoria, $documentoPdf])) {
                $idAuditoria = $pdo->lastInsertId();   // salva em variável
                $_SESSION['id_auditoria'] = $idAuditoria;

                header("Location: index.php?rota=checklist&id_auditoria=" . $idAuditoria);
                exit;
            } else {
                throw new Exception("Erro ao inserir auditoria");
            }
        } catch (Exception $e) {
            error_log("Erro ao criar auditoria: " . $e->getMessage());
            header('Location: index.php?rota=auditoria_criacao&erro=1');
            exit;
        }
    }
}
?>
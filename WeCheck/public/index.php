<?php
session_start();
require_once __DIR__ . '/../controllers/CadastroController.php';
require_once __DIR__ . '/../controllers/auditoriaCriacaoController.php';

$rota = $_GET['rota'] ?? 'home';

switch ($rota) {
    case 'cadastro':
        CadastroController::cadastrar(); 
        break;

    case 'criar_auditoria':
        session_start();
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idUsuario) {
            $nomeAuditoria = $_POST['nome_auditoria'];
            $empresaAuditoria = $_POST['empresa_auditoria'];

            // Upload do PDF
            $documentoPdf = null;
            if (isset($_FILES['documento_pdf']) && $_FILES['documento_pdf']['error'] === UPLOAD_ERR_OK) {
                $nomeArquivo = time() . "_" . basename($_FILES['documento_pdf']['name']);
                $destino = __DIR__ . '/../uploads/' . $nomeArquivo;
                move_uploaded_file($_FILES['documento_pdf']['tmp_name'], $destino);
                $documentoPdf = $nomeArquivo;
            }

            AuditoriaCriacaoController::criarAuditoria(
                $idUsuario, 
                $nomeAuditoria, 
                $empresaAuditoria, 
                $documentoPdf
            );
        }
        break;

    case 'auditoria_criacao':
        require __DIR__ . '/../views/auditoria_criacao_view.php';
        break;

    case 'home':
    default:
        require_once __DIR__ . '/../views/home_view.php';
        break;
}

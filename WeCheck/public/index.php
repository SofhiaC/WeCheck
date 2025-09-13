<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/db.php"; 

$rota = $_GET['rota'] ?? 'home';

switch ($rota) {
    case 'cadastro':
        require_once __DIR__ . '/../controllers/CadastroController.php';
        CadastroController::cadastrar(); 
        break;

    case 'criar_auditoria':
        require_once __DIR__ . '/../controllers/AuditoriaCriacaoController.php';
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idUsuario) {
            $nomeAuditoria = $_POST['nome_auditoria'];
            $empresaAuditoria = $_POST['empresa_auditoria'];

            // Crie o diretório de uploads se não existir
            $uploadDir = __DIR__ . '/../uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Upload do PDF
            $documentoPdf = null;
            if (isset($_FILES['documento_pdf']) && $_FILES['documento_pdf']['error'] === UPLOAD_ERR_OK) {
                $nomeArquivo = time() . "_" . basename($_FILES['documento_pdf']['name']);
                $destino = $uploadDir . $nomeArquivo;
                if (move_uploaded_file($_FILES['documento_pdf']['tmp_name'], $destino)) {
                    $documentoPdf = $nomeArquivo;
                }
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
    
    case 'auditorias': 
        require_once __DIR__ . '/../controllers/AuditoriaController.php';
        $auditorias = AuditoriaController::listarAuditorias();
        require __DIR__ . '/../views/auditorias_view.php';
        break;

    case 'home':
    default:
        require_once __DIR__ . '/../views/home_view.php';
        break;

    case 'checklist':
    require_once __DIR__ . '/../controllers/ChecklistController.php';

    $idAuditoria = $_SESSION['id_auditoria'] ?? null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idAuditoria) {
        $nomeItem = $_POST['nome_item'] ?? null;

        if ($nomeItem) {
            ChecklistController::adicionarItem($idAuditoria, $nomeItem);
        }
    }

    // lista os itens dessa auditoria
    $itens = ChecklistController::listarItens($idAuditoria);
    require __DIR__ . '/../views/checklist_view.php';
    break;
}
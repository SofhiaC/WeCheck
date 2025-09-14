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
        require_once __DIR__ . '/../controllers/ListarAuditoriaController.php';
        $auditorias = ListarAuditoriaController::listarAuditorias();
        require __DIR__ . '/../views/auditorias_view.php';
        break;

    case 'home':
    default:
        require_once __DIR__ . '/../views/home_view.php';
        break;

    case 'checklist':
    require_once __DIR__ . '/../controllers/ChecklistController.php';
    require_once __DIR__ . '/../controllers/ListarAuditoriaController.php';

    $idAuditoria = $_SESSION['id_auditoria'] ?? null;

    if (!$idAuditoria) {
        header('Location: index.php?rota=auditorias');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nomeItem = $_POST['nome_item'] ?? null;

        if ($nomeItem) {
            ChecklistController::adicionarItem($idAuditoria, $nomeItem);
            // opcional: definir uma variável para mostrar mensagem
            $mensagem = "Item adicionado com sucesso!";
        }
    }

    $auditoria = ListarAuditoriaController::pegarAuditoria($idAuditoria);
    $itens = ChecklistController::listarItens($idAuditoria);

    require __DIR__ . '/../views/checklist_view.php';
    break;


    case 'adicionar_item':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idAuditoria = $_POST['id_auditoria'] ?? null;
        $nomeItem = $_POST['nome_item'] ?? null;

        if ($idAuditoria && $nomeItem) {
            require_once __DIR__ . '/../controllers/ChecklistController.php';
            $resultado = ChecklistController::adicionarItem($idAuditoria, $nomeItem);

            if ($resultado['success']) {
                header("Location: index.php?rota=checklist"); // volta para a página de checklist
                exit;
            } else {
                die($resultado['message']);
            }
        }
    }
    break;

    case 'adicionar_responsavel':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idAuditoria = $_SESSION['id_auditoria'] ?? null;
        $nome = $_POST['nome_responsavel'] ?? null;
        $email = $_POST['email_responsavel'] ?? null;
        $cargo = $_POST['cargo_responsavel'] ?? null;

        if ($idAuditoria && $nome && $email) {
            require_once __DIR__ . '/../controllers/ResponsavelController.php';
            $resultado = ResponsavelController::adicionarResponsavel($idAuditoria, $nome, $email, $cargo);

            if ($resultado['success']) {
                header("Location: index.php?rota=checklist"); // volta para checklist
                exit;
            } else {
                die($resultado['message']);
            }
        }
    }
    break;

    case 'processo_auditoria':
        $idAuditoria = $_SESSION['id_auditoria'] ?? null;

        if (!$idAuditoria) {
            header('Location: index.php?rota=auditorias');
            exit;
        }

        require_once __DIR__ . '/../controllers/ProcessoAuditoriaController.php';

        $auditoria = ProcessoAuditoriaController::pegarAuditoria($idAuditoria);
        $itens = ProcessoAuditoriaController::listarItensAuditoria($idAuditoria);

        $arquivo = __DIR__ . '/../views/processo_auditoria_view.php';
            if (!file_exists($arquivo)) {
                die("Arquivo da view não encontrado em: $arquivo");
            } else {
                require $arquivo;
            }
        break;

        case 'atualizar_resultado':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $idItem = $_POST['id_item'] ?? null;
                $resultado = $_POST['resultado'] ?? null;

                if ($idItem && $resultado) {
                    require_once __DIR__ . '/../controllers/ProcessoAuditoriaController.php';
                    $sucesso = ProcessoAuditoriaController::atualizarResultado($idItem, $resultado);
                    echo $sucesso ? 'ok' : 'erro';
                }
            }
            exit;

        case 'salvar_nc':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $idItem       = $_POST['id_item'] ?? null;
                $classificacao = $_POST['classificacao'] ?? null;
                $acaoCorretiva = $_POST['acao_corretiva'] ?? null;
                $observacao    = $_POST['observacao'] ?? null;
                $responsavel   = $_POST['id_responsavel'] ?? null;
                $dataInicial   = $_POST['data_inicial'] ?? null;
                $dataConclusao = $_POST['data_conclusao'] ?? null;

                if ($idItem && $classificacao && $acaoCorretiva && $responsavel && $dataInicial) {
                    require_once __DIR__ . '/../controllers/ProcessoAuditoriaController.php';

                    $sucesso = ProcessoAuditoriaController::salvarNaoConformidade(
                        $idItem, 
                        $classificacao,
                        $acaoCorretiva, 
                        $observacao, 
                        $responsavel, 
                        $dataInicial, 
                        $dataConclusao
                    );

                    echo $sucesso ? 'ok' : 'erro';
                } else {
                    echo 'erro';
                }
            }
            exit;

    case 'listar_ncs':
        require_once __DIR__ . '/../views/listar_ncs_view.php';
        break;

    case 'normalizar_nc':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idNc = $_POST['id_nc'] ?? null;
            $status = $_POST['status'] ?? null;
            $observacao = $_POST['observacao'] ?? null;
            $dataConclusao = $_POST['data_conclusao'] ?? null;

            if ($idNc && $status && $dataConclusao) {
                require_once __DIR__ . '/../controllers/ProcessoAuditoriaController.php';
                $sucesso = ProcessoAuditoriaController::normalizarNc($idNc, $status, $observacao, $dataConclusao);
                echo $sucesso ? 'ok' : 'erro';
            } else {
                echo 'erro';
            }
        }
        exit;

}
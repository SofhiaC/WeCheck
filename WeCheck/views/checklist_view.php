<?php

$idAuditoria = $_SESSION['id_auditoria'] ?? null;

if (!$idAuditoria) {
    header('Location: index.php?rota=auditorias');
    exit;
}

require_once __DIR__ . '/../controllers/ListarAuditoriaController.php';
require_once __DIR__ . '/../controllers/ChecklistController.php';
require_once __DIR__ . '/../controllers/ResponsavelController.php';

// Pegar dados da auditoria
$auditoria = ListarAuditoriaController::pegarAuditoria($idAuditoria);
if (!$auditoria) die("Auditoria não encontrada.");

// Pegar itens e responsáveis
$itens = ChecklistController::listarItens($idAuditoria);
$responsaveis = ResponsavelController::listarResponsaveis($idAuditoria);
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/checklist_criacao.css">
    <link rel="stylesheet" href="../assets/css/modal_criacaochecklist.css">
    <title>WeCheck</title>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../assets/logo/WeCheck_Logo.png" alt="Logo WeCheck">
            <img src="../assets/logo/WeCheck_Escrita.png" alt="Nome WeCheck">
        </div>
        <div class="botoes-nav">
            <a id="inicio" href="index.php?rota=auditorias">Início</a>
            <a id="conta" href="#">Conta</a>
        </div>
    </header>

    <main>
        <h1><?php echo htmlspecialchars($auditoria['nome_auditoria']); ?></h1>
        <p><?php echo htmlspecialchars($auditoria['empresa_auditoria']); ?></p>

        <div class="controle-responsaveis">
            <p>Adicione os responsáveis pelo projeto da empresa</p>
            <div class="botoes-responsaveis">
                <button id="btnAdicionarResponsavel" type="button">Adicionar Responsável</button>
                <button id="btnVizualizarResponsavel" type="button">Visualizar Responsáveis</button>
            </div>
        </div>

        <br>
        <h3>Liste os itens do CheckList</h3>

        <button id="btnAdicionarItem" type="button">
            <div class="botao-adicionar-item">
                <img src="../assets/icons/AdicaoItem.png" alt="Icone de adição">
                <p>Adicionar Item</p>
            </div>
        </button>

        <ul>
            <?php foreach ($itens as $item): ?>
                <li>
                    <span><?php echo htmlspecialchars($item['ordem_item']); ?></span>
                    <span><?php echo htmlspecialchars($item['nome_item']); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <br>
    </main>

    <footer>
        <div>
            <p><?php echo htmlspecialchars(count($itens)) . " itens no checklist"; ?></p>
            <a href="index.php?rota=processo_auditoria">Iniciar Auditoria</a>
        </div>
    </footer>

    <!-- DIALOGS (modais) -->
    <!-- Modal Adicionar Item -->
    <dialog id="modalItem" class="dialog">
        <div class="modal-conteudo">
            <button class="fechar" type="button" aria-label="Fechar">&times;</button>
            <img src="../assets/icons/AdicaoItem.png" alt="Icone de adição">
            <h2>Adicionar Item</h2>
            <form id="formAdicionarItem" method="POST" action="index.php?rota=checklist">
                <input type="hidden" name="id_auditoria" value="<?php echo htmlspecialchars($idAuditoria); ?>">
                <label>Nome do Item:<br>
                    <input type="text" name="nome_item" required>
                </label>
                <br><br>
                <button type="submit">Adicionar</button>
            </form>
        </div>
    </dialog>

    <!-- Modal Adicionar Responsável -->
    <dialog id="modalResponsavel" class="dialog">
        <div class="modal-conteudo">
            <button class="fechar" type="button" aria-label="Fechar">&times;</button>
            <img src="../assets/icons/AdicaoResponsavel.png" alt="Icone de perfil">
            <h2>Adicionar Responsável</h2>
            <form id="formAdicionarResponsavel" method="POST" action="index.php?rota=adicionar_responsavel">
                <input type="hidden" name="id_auditoria" value="<?php echo htmlspecialchars($idAuditoria); ?>">
                <label>Email:<br>
                    <input type="email" name="email_responsavel" required>
                </label>
                <br>
                <label>Nome:<br>
                    <input type="text" name="nome_responsavel" required>
                </label>
                <br>
                <label>Cargo:<br>
                    <input type="text" name="cargo_responsavel" required>
                </label>
                <br>
                <label>Setor:<br>
                    <input type="text" name="setor_responsavel" required>
                </label>
                <br><br>
                <button type="submit">Adicionar</button>
            </form>
        </div>
    </dialog>

    <!-- Modal Visualizar Responsáveis -->
    <dialog id="modalVizualizarResponsavel" class="dialog">
        <div class="modal-conteudo">
            <button class="fechar" type="button" aria-label="Fechar">&times;</button>
            <img src="../assets/icons/ListaResponsaveis.png" alt="Icone de grupo">
            <h2>Responsáveis</h2>
            <ul class="lista-responsaveis">
                <?php foreach ($responsaveis as $resp): ?>
                    <li>
                        <?php echo htmlspecialchars($resp['nome_responsavel']); ?>
                        (<?php echo htmlspecialchars($resp['email_responsavel']); ?>) -
                        <?php echo htmlspecialchars($resp['cargo_responsavel']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </dialog>

    <!-- JS -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function setupModal(btnId, dialogId) {
            const btn = document.getElementById(btnId);
            const dlg = document.getElementById(dialogId);
            if (!btn || !dlg) return;

            const closeBtn = dlg.querySelector('.fechar');

            // Se navegador suporta dialog nativo
            if (typeof HTMLDialogElement === 'function' || typeof HTMLDialogElement === 'object') {
                btn.addEventListener('click', () => {
                    try { dlg.showModal(); } catch (e) { dlg.setAttribute('open',''); }
                });

                closeBtn && closeBtn.addEventListener('click', () => dlg.close());

                // clique na sombra do backdrop fecha
                dlg.addEventListener('click', (e) => {
                    if (e.target === dlg) dlg.close();
                });

                // ESC
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && dlg.open) dlg.close();
                });
            } else {
                // fallback simples para navegadores sem <dialog>
                btn.addEventListener('click', () => {
                    dlg.setAttribute('data-open', 'true');
                    document.body.classList.add('dialog-open');
                });

                closeBtn && closeBtn.addEventListener('click', () => {
                    dlg.removeAttribute('data-open');
                    document.body.classList.remove('dialog-open');
                });

                dlg.addEventListener('click', (e) => {
                    if (e.target === dlg) {
                        dlg.removeAttribute('data-open');
                        document.body.classList.remove('dialog-open');
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && dlg.hasAttribute('data-open')) {
                        dlg.removeAttribute('data-open');
                        document.body.classList.remove('dialog-open');
                    }
                });
            }
        }

        setupModal('btnAdicionarItem', 'modalItem');
        setupModal('btnAdicionarResponsavel', 'modalResponsavel');
        setupModal('btnVizualizarResponsavel', 'modalVizualizarResponsavel');
    });
    </script>
</body>
</html>

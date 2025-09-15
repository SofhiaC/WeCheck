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
                <button id="btnCadastrarSetor" type="button">Cadastrar setor</button>
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
    <!-- modal cadastrar setor -->
    <dialog id="modalaSetor" class="dialog" aria-labelledby="tituloSetor">
        <div class="modal-conteudo" role="document">
            <button class="fechar" type="button" aria-label="Fechar">&times;</button>

            <img src="../assets/icons/Setor.png" alt="Ícone setor">
            <h2 id="tituloSetor">Cadastrar Setor</h2>

            <form method="POST" action="index.php?rota=setor">
            <!-- garantir que o id da auditoria vem -->
            <input type="hidden" name="id_auditoria" value="<?php echo htmlspecialchars($idAuditoria); ?>">

            <label>Nome Setor:<br>
                <input type="text" name="nome_setor" required>
            </label>

            <label>Gerente Responsável:<br>
                <input type="text" name="gerente_responsavel" required>
            </label>

            <label>Email gerente:<br>
                <input type="email" name="email_gerente" required>
            </label>

            <div style="display:flex; gap:.5rem; justify-content:center; margin-top:12px;">
                <button type="submit">Cadastrar</button>
            </div>
            </form>
        </div>
        </dialog>

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
                    <select name="id_setor" required>
                        <option value="">-- Selecione um setor --</option>
                        <?php 
                        require_once __DIR__ . '/../controllers/SetorController.php';
                        $setores = SetorController::listarSetores($idAuditoria);
                        foreach ($setores as $setor): ?>
                            <option value="<?= $setor['id_setor'] ?>">
                                <?= htmlspecialchars($setor['nome_setor']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
                        <strong><?php echo htmlspecialchars($resp['nome_responsavel']); ?></strong>
                        (<?php echo htmlspecialchars($resp['email_responsavel']); ?>) -
                        <?php echo htmlspecialchars($resp['cargo_responsavel']); ?>
                        <?php if (!empty($resp['nome_setor'])): ?>
                            <em>[<?php echo htmlspecialchars($resp['nome_setor']); ?>]</em>
                        <?php endif; ?>
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

            const closeBtns = dlg.querySelectorAll('.fechar');

            // garante que exista um X visível
            closeBtns.forEach(b => { if (!b.innerHTML.trim()) b.innerHTML = '&times;'; });

            function openDialog() {
                // abrir dialog nativo quando disponível
                if (typeof dlg.showModal === 'function') {
                    try {
                        dlg.showModal();
                    } catch (e) {
                        dlg.setAttribute('open','');
                    }
                    document.body.classList.add('dialog-open');
                } else {
                    // fallback simples
                    dlg.setAttribute('data-open', 'true');
                    document.body.classList.add('dialog-open');
                }

                // foco no primeiro input do modal
                const first = dlg.querySelector('input,select,textarea,button');
                if (first) first.focus();
            }

            function closeDialog() {
                if (typeof dlg.close === 'function') {
                    try { dlg.close(); } catch (e) { dlg.removeAttribute('open'); }
                } else {
                    dlg.removeAttribute('data-open');
                }
                document.body.classList.remove('dialog-open');
                btn.focus();
            }

            btn.addEventListener('click', openDialog);

            // fechar por todos os botões .fechar
            closeBtns.forEach(cb => cb.addEventListener('click', closeDialog));

            // clique fora (backdrop)
            dlg.addEventListener('click', (e) => {
                if (e.target === dlg) closeDialog();
            });

            // ESC (fallback)
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && (dlg.hasAttribute('open') || dlg.hasAttribute('data-open'))) {
                    closeDialog();
                }
            });

            // quando cancelar (comportamento nativo do dialog)
            dlg.addEventListener('cancel', (e) => {
                document.body.classList.remove('dialog-open');
            });
        }

        // registrar seus modais (adicione aqui novos modais se criar outros)
        setupModal('btnCadastrarSetor', 'modalaSetor');
        setupModal('btnAdicionarItem', 'modalItem');
        setupModal('btnAdicionarResponsavel', 'modalResponsavel');
        setupModal('btnVizualizarResponsavel', 'modalVizualizarResponsavel');
    });
    </script>
</body>
</html>

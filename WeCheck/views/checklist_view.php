<?php
$idAuditoria = $_SESSION['id_auditoria'] ?? null;

if (!$idAuditoria) {
    header('Location: index.php?rota=auditorias');
    exit;
}

require_once __DIR__ . '/../controllers/AuditoriaController.php';
require_once __DIR__ . '/../controllers/ChecklistController.php';

// Pegar dados da auditoria
$auditoria = AuditoriaController::pegarAuditoria($idAuditoria);
if (!$auditoria) die("Auditoria não encontrada.");

// Pegar itens do checklist
$itens = ChecklistController::listarItens($idAuditoria);
?>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/modal_checklist.css">
    <title>WeCheck</title>
    
</head>
<body>
    <header>
        <img src="../assets/logo/WeCheck_Logo.png" alt="Logo WeCheck"> 
        <img src="../assets/logo/WeCheck_Escrita.png" alt="Nome WeCheck">
        <a href="index.php?rota=auditorias">Início</a> 
        <a href="#">Conta</a>
    </header>

    <main>
        <h1><?php echo htmlspecialchars($auditoria['nome_auditoria']); ?></h1>
        <p><?php echo htmlspecialchars($auditoria['empresa_auditoria']); ?></p>

        <p>Adicione os responsáveis pelo projeto da empresa:</p>
        <button id="btnAdicionarResponsavel">Adicionar Responsável</button>
        <button id="btnVizualizarResponsavel">Visualizar Responsáveis</button>

        <h3>Liste os itens do CheckList </h3>
        <div>
            <img src="../assets/icons/AdicaoItem.png" alt="Icone de adição">
            <button id="btnAdicionarItem">Adicionar Item</button>
        </div>

        <ul>
            <?php foreach ($itens as $item): ?>
                <li>
                    <?php echo $item['ordem_item']; ?> 
                    <?php echo htmlspecialchars($item['nome_item']); ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <div>
            <?php echo htmlspecialchars(count($itens)) . " itens no checklist."; ?>
            <a href="#">Iniciar Auditoria</a>
        </div>
    </main>

    <footer>
        <p>© 2025 WeCheck. Por Midup.</p>
    </footer>

    <!-- Modais -->

    <!-- Modal Adicionar Item -->
    <div id="modalItem" class="modal">
        <div class="modal-conteudo">
            <span class="fechar">&times;</span>
            <img src="../assets/icons/AdicaoItem.png" alt="Icone de adição">
            <h2>Adicionar Item</h2>
            <form id="formAdicionarItem" method="POST" action="index.php?rota=checklist">
                <label>Nome do Item: <input type="text" name="nome_item" required></label>
                <button type="submit">Adicionar</button>
            </form>
        </div>
    </div>

    <!-- Modal Adicionar Responsável -->
    <div id="modalResponsavel" class="modal">
        <div class="modal-conteudo">
            <span class="fechar">&times;</span>
            <img src="../assets/icons/AdicaoResponsavel.png" alt="Icone de perfil">
            <h2>Adicionar Responsável</h2>
            <form id="formAdicionarResponsavel" method="POST" action="index.php?rota=adicionar_responsavel">
                <label>Email: <br>
                <input type="email" name="email_responsavel" required></label>
                <br>
                <label>Nome: <br>
                <input type="text" name="nome_responsavel" required></label>
                <br>
                <label>Cargo: <br>
                <input type="text" name="cargo_responsavel" required></label>
                <br>
                <label>Setor: <br>
                <input type="text" name="setor_responsavel" required>
                <br><br>
                <button type="submit">Adicionar</button>
            </form>
        </div>
    </div>

    <!-- Modal Visualizar Responsáveis -->
    <div id="modalVizualizarResponsavel" class="modal">
        <div class="modal-conteudo">
            <span class="fechar">&times;</span>
            <img src="../assets/icons/ListaResponsaveis.png" alt="Icone de grupo">
            <h2>Responsáveis</h2>
            <ul>
                <?php
                require_once __DIR__ . '/../controllers/ResponsavelController.php';
                $responsaveis = ResponsavelController::listarResponsaveis($idAuditoria);

                foreach ($responsaveis as $resp):
                ?>
                    <li><?php echo htmlspecialchars($resp['nome_responsavel']); ?> 
                        (<?php echo htmlspecialchars($resp['email_responsavel']); ?>) - 
                        <?php echo htmlspecialchars($resp['cargo_responsavel']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        function abrirModal(botaoId, modalId) {
            const botao = document.getElementById(botaoId);
            const modal = document.getElementById(modalId);
            const fechar = modal.querySelector('.fechar');

            botao.addEventListener('click', () => modal.style.display = 'block');
            fechar.addEventListener('click', () => modal.style.display = 'none');

            window.addEventListener('click', (e) => {
                if (e.target === modal) modal.style.display = 'none';
            });
        }

        abrirModal('btnAdicionarItem', 'modalItem');
        abrirModal('btnAdicionarResponsavel', 'modalResponsavel');
        abrirModal('btnVizualizarResponsavel', 'modalVizualizarResponsavel');
    </script>
</body>
</html>

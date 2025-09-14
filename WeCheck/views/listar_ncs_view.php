<?php
$idAuditoria = $_GET['id_auditoria'] ?? null;
if (!$idAuditoria) {
    header('Location: index.php?rota=auditorias');
    exit;
}

require_once __DIR__ . '/../controllers/ProcessoAuditoriaController.php';
require_once __DIR__ . '/../controllers/ListarAuditoriaController.php';

// Pegar dados da auditoria
$auditoria = ListarAuditoriaController::pegarAuditoria($idAuditoria);
if (!$auditoria) {
    die("Auditoria não encontrada.");
}

// Pegar NCs da auditoria
$ncs = ProcessoAuditoriaController::listarNaoConformidades($idAuditoria);
?>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/nc_modal.css">
    <link rel="stylesheet" href="../assets/css/lista_nc.css">

    
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

        <h3>Não conformidades pendentes</h3>

        <table>
            <thead>
                <tr>
                    <th>ID do Item</th>
                    <th>Nome do Item</th>
                    <th>Responsável</th>
                    <th>Data de Início</th>
                    <th>Data Final</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($ncs as $nc): ?>
            <tr data-id-nc="<?php echo $nc['id_nc']; ?>">
                <td><?php echo (string) $nc['id_nc']; ?></td>
                <td><?php echo htmlspecialchars($nc['nome_item']); ?></td>
                <td><?php echo htmlspecialchars($nc['nome_responsavel']); ?></td>
                <td><?php echo htmlspecialchars($nc['data_inicial']); ?></td>
                <td><?php echo htmlspecialchars($nc['data_conclusao']); ?></td>
                <td class="acao-nc">
                    <button class="btn-normalizar">Normalizar</button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- ------------- Modal (único, fora do loop) ------------- -->
        <?php if (count($ncs) > 0): ?>
            <div id="modalNormalizacao" class="modal" style="display:none;">
                <div class="modal-conteudo">
                    <span id="fecharModalNormalizacao" class="fechar">&times;</span>
                    <img src="../assets/icons/ResultadosCumprem.png" alt="Icone de conformidade">
                    <h2>Normalizar Não Conformidade</h2>
                    <form id="formNormalizacao">
                        <input type="hidden" id="id_nc_normalizacao" name="id_nc">

                        <label>Nome do Item:
                            <input type="text" id="nome_item_nc_normalizacao" name="nome_item" readonly>
                        </label>

                        <label>Status:
                            <select id="status_nc" name="status" required>
                                <option value="">Selecione</option>
                                <option value="resolvida">Conforme</option>
                            </select>
                        </label>

                        <label>Observação: <br>
                            <textarea name="observacao" rows="3" cols="50"></textarea>
                        </label>

                        <label>Data de conclusão:
                            <input type="date" id="data_conclusao_nc_normalizacao" name="data_conclusao" required>
                        </label>

                        <button type="button" id="salvarNormalizacao">Salvar Resultado</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        
        <?php
        $itens = ProcessoAuditoriaController::listarItensAuditoria($idAuditoria);

        // Calcular aderência (mesma lógica que você já usa)
        $itensAvaliaveis = array_filter($itens, fn($i) => ($i['resultado_item'] ?? '') !== 'nao_aplicavel');
        $itensCumpridos = array_filter($itensAvaliaveis, fn($i) => ($i['resultado_item'] ?? '') === 'conforme');

        $aderencia = count($itensAvaliaveis) > 0 
            ? round(count($itensCumpridos) / count($itensAvaliaveis) * 100, 1) 
            : 0;

        $itensRestantes = count(array_filter($itens, fn($i) => empty($i['resultado_item'])));

        ?>

        <div class="aderencia">
            <p>Aderência: <span id="aderencia"><?= $aderencia ?></span>%</p>
        </div>


        <script>
        const modalNormalizacao = document.getElementById('modalNormalizacao');
        const fecharModalNormalizacao = document.getElementById('fecharModalNormalizacao');
        let ncAtual = null;

        // Garantir que o modal comece fechado
        if (modalNormalizacao) modalNormalizacao.style.display = 'none';

        // Abrir modal ao clicar em botão "Normalizar"
        document.querySelectorAll('.btn-normalizar').forEach(btn => {
            btn.addEventListener('click', e => {
                const row = e.target.closest('tr');
                const ncId = row.dataset.idNc;

                // Preencher dados do modal com a NC clicada
                document.getElementById('id_nc_normalizacao').value = ncId;
                document.getElementById('nome_item_nc_normalizacao').value = row.children[1].innerText;

                ncAtual = ncId;

                // Abrir modal
                modalNormalizacao.style.display = 'flex';
            });
        });

        // Fechar modal
        if (fecharModalNormalizacao) {
            fecharModalNormalizacao.addEventListener('click', () => {
                modalNormalizacao.style.display = 'none';
            });
        }

        // Salvar NC normalizada via fetch
        document.getElementById('salvarNormalizacao').addEventListener('click', () => {
            const formData = new FormData(document.getElementById('formNormalizacao'));

            fetch('index.php?rota=normalizar_nc', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(res => {
                if (res.trim() === 'ok') {
                    alert('NC atualizada com sucesso!');
                    modalNormalizacao.style.display = 'none';

                    // Remove a linha da tabela
                    const row = document.querySelector(`tr[data-id-nc="${ncAtual}"]`);
                    if (row) row.remove();
                } else {
                    alert('Erro ao atualizar NC.');
                }
            });
        });

            // Atualizar aderência e itens restantes
            function atualizarAderencia() {
                const linhas = document.querySelectorAll('table tbody tr');
                let conformes = 0;
                let restantes = 0;

                linhas.forEach(row => {
                    const status = row.querySelector('.acao-nc').innerText.toLowerCase();
                    if (status.includes('resolvida')) conformes++;
                    else restantes++;
                });

                // Atualiza a view
                document.getElementById('aderencia').innerText = conformes; // recalcular % se quiser
                const itensRestantesEl = document.getElementById('itens-restantes');
                if (itensRestantesEl) itensRestantesEl.innerText = restantes;
            }
        </script>

    </main>
</body>
</html>

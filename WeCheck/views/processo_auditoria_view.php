<?php
$idAuditoria = $_SESSION['id_auditoria'] ?? null;

if (!$idAuditoria) {
    header('Location: index.php?rota=auditorias');
    exit;
}

require_once __DIR__ . '/../controllers/ListarAuditoriaController.php';
require_once __DIR__ . '/../controllers/ChecklistController.php';
require_once __DIR__ . '/../controllers/ProcessoAuditoriaController.php';

// Pegar dados da auditoria
$auditoria = ListarAuditoriaController::pegarAuditoria($idAuditoria);
if (!$auditoria) die("Auditoria não encontrada.");

// Pegar itens do checklist
$itens = ChecklistController::listarItens($idAuditoria);

// Pegar NCs da auditoria
$ncs = ProcessoAuditoriaController::listarNaoConformidades($idAuditoria); 

// Contar por classificação
$contNcs = ['leve' => 0, 'moderado' => 0, 'urgente' => 0];
foreach ($ncs as $nc) {
    $class = $nc['classificacao_nc'] ?? null;
    if ($class && isset($contNcs[$class])) {
        $contNcs[$class]++;
    }
}
?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/auditoria_processo.css">
    <link rel="stylesheet" href="../assets/css/nc_modal.css">
    <link rel="stylesheet" href="../assets/css/checklist_criacao.css">
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

        <div id="resultados" class="resultados-painel">
            <p>Resultados</p>

            <img src="../assets/icons/ResultadosCumprem.png" alt="Icone de resultados">
            <div><span id="cont-conforme">0</span> Cumprem</div>
            <img src="../assets/icons/ResultadoNaoSeAplica.png" alt="Icone de resultados">
            <div><span id="cont-nao-aplicavel">0</span> Não se aplica</div>
            <img src="../assets/icons/ResultadoNaoCumpre.png" alt="Icone de resultados">
            <div><span id="cont-nao-conforme">0</span> Não cumpre</div>
        </div>
        <br>
        <div id="nfc">
            <p>NFCs</p>

            <img src="../assets/icons/NFCLeve.png" alt="Icone de NFCs">
            <p> <span id="cont-nc-leve"><?php echo $contNcs['leve']; ?></span> Leve</p>

            <img src="../assets/icons/NFCModerada.png" alt="Icone de NFCs">
            <p> <span id="cont-nc-moderado"><?php echo $contNcs['moderado']; ?></span> Moderada</p>

            <img src="../assets/icons/NFCUrgente.png" alt="Icone de NFCs">
            <p> <span id="cont-nc-urgente"><?php echo $contNcs['urgente']; ?></span> Urgente</p>
        </div>
        <br>

        <h3>Itens do Checklist</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome do Item</th>
                    <th>Resultado</th>
                    <th>Seleção</th>
                </tr>
            </thead>

            <?php 
            $mapVisual = [
                'conforme' => 'Cumpre',
                'nao_aplicavel' => 'Não se Aplica',
                'nao_conforme' => 'Não Cumpre'
            ];
            ?>

            <tbody>
                <?php foreach ($itens as $item): ?>
                <tr>
                    <td data-label="Nome do Item"><?php echo $item['ordem_item']; ?></td>
                    <td data-label="Resultado"><?php echo htmlspecialchars($item['nome_item']); ?></td>
                    <td data-label="Seleção" id="resultado-<?php echo $item['id_item']; ?>">
                        <?php 
                            $resultado = $item['resultado_item'] ?? null;
                            echo isset($mapVisual[$resultado]) ? $mapVisual[$resultado] : '-';
                        ?>
                    </td>
                    <td>
                        <button class="btn-resultado" data-id="<?php echo $item['id_item']; ?>">Resultado</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="menu-resultado" class="menu-resultado" style="display:none; position:absolute; background:#fff; border:1px solid #ccc;">
            <button data-valor="conforme">Cumpre</button>
            <button data-valor="nao_aplicavel">Não se Aplica</button>
            <button data-valor="nao_conforme">Não Cumpre</button>
        </div>


        <!-- Modal de Não Conformidade -->
        <div id="modalNaoConformidade" class="modal" style="display:none;">
            <div class="modal-conteudo">
                <span id="fecharModal" class="fechar">&times;</span>
                <img src="../assets/icons/NaoConformidade.png" alt="Icone de NFCs">
                <h2>Registrar Não Conformidade</h2>
                <form id="formNaoConformidade">
                    <input type="hidden" id="id_item_nc" name="id_item">

                    <label>Nome do Item:
                        <input type="text" name="nome_item" id="nome_item_nc" readonly>
                    </label>
            

                    <label>Classificação: 
                        <select id="classificacao_nc" name="classificacao" required>
                            <option value="">Selecione</option>
                            <option value="leve">Leve</option>
                            <option value="moderado">Moderado</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </label>

                    <label>Ação corretiva: <br>
                        <textarea name="acao_corretiva" rows="2" cols="50" required></textarea>
                    </label>

                    <label>Observação: <br>
                        <textarea name="observacao" rows="2" cols="50"></textarea>
                    </label>

                    <label>Responsável: 
                        <select name="id_responsavel" required>
                            <option value="">Selecione</option>
                            <?php
                            require_once __DIR__ . '/../controllers/ResponsavelController.php';
                            $responsaveis = ResponsavelController::listarResponsaveis($idAuditoria);
                            foreach($responsaveis as $r){
                                echo "<option value='{$r['id_responsavel']}'>{$r['nome_responsavel']}</option>";
                            }
                            ?>
                        </select>
                    </label>

                    <label>Data inicial:
                        <input type="date" name="data_inicial" required>
                    </label>

                    <label>Data de conclusão:
                        <input type="date" id="data_conclusao_nc" name="data_conclusao">
                    </label>

                    <button type="button" id="salvarNC">Enviar NC</button>
                </form>
            </div>
        </div>






        <script>
            const menu = document.getElementById('menu-resultado');
            let itemAtual = null;

            // Mapeamento para exibição visual
            const mapVisual = {
                'conforme': 'Cumpre',
                'nao_aplicavel': 'Não se Aplica',
                'nao_conforme': 'Não Cumpre'
            };

            // Array de itens vindo do PHP
            const itens = <?php echo json_encode(array_map(function($i){
                $i['resultado_item'] = $i['resultado_item'] ?? null;
                return $i;
            }, $itens)); ?>;

            // Abre o menu ao clicar no botão de resultado
            document.querySelectorAll('.btn-resultado').forEach(btn => {
                btn.addEventListener('click', (e) => {
                itemAtual = e.target.dataset.id;

                const rect = e.target.getBoundingClientRect();
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                const scrollLeft = window.scrollX || document.documentElement.scrollLeft;

                menu.style.display = 'block';
                menu.style.top = (rect.bottom + scrollTop) + 'px';
                menu.style.left = (rect.left + scrollLeft) + 'px';
});
            });

            // Ao clicar em uma opção do menu
            menu.querySelectorAll('button').forEach(opcao => {
                opcao.addEventListener('click', () => {
                    const valor = opcao.dataset.valor;

                    // Atualiza visualmente a tabela
                    document.getElementById('resultado-' + itemAtual).innerText = mapVisual[valor];

                    // Atualiza o array local
                    const itemObj = itens.find(i => i.id_item == itemAtual);
                    if(itemObj) itemObj.resultado_item = valor;

                    // Atualiza os contadores
                    atualizarContadores();
                    atualizarItensRestantes();

                    // Fecha o menu
                    menu.style.display = 'none';

                    // Agora chama a função que envia para o servidor E abre o modal se necessário
                    salvarResultado(itemAtual, valor);
                });
            });

            // Fecha menu se clicar fora
            window.addEventListener('click', (e) => {
                if (!menu.contains(e.target) && !e.target.classList.contains('btn-resultado')) {
                    menu.style.display = 'none';
                }
            });

            // Função para atualizar contadores na div de resultados
            function atualizarContadores() {
                let conforme = 0;
                let naoAplicavel = 0;
                let naoConforme = 0;

                itens.forEach(item => {
                    switch(item.resultado_item) {
                        case 'conforme': conforme++; break;
                        case 'nao_aplicavel': naoAplicavel++; break;
                        case 'nao_conforme': naoConforme++; break;
                    }
                });

                document.getElementById('cont-conforme').innerText = conforme;
                document.getElementById('cont-nao-aplicavel').innerText = naoAplicavel;
                document.getElementById('cont-nao-conforme').innerText = naoConforme;
            }

            // Atualiza contadores ao carregar a página
            atualizarContadores();

            function salvarResultado(idItem, valor) {
                fetch('index.php?rota=atualizar_resultado', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id_item=${idItem}&resultado=${valor}`
                })
                .then(res => res.text())
                .then(res => {
                    if (res.trim() === 'ok') {
                        atualizarContadores();

                        if (valor === 'nao_conforme') {
                            abrirModal(itemAtual);
                        }
                    }
                });
            }

            function abrirModal(idItem) {
                const itemObj = itens.find(i => i.id_item == idItem);
                if (!itemObj) return;

                document.getElementById('id_item_nc').value = idItem;
                document.getElementById('nome_item_nc').value = itemObj.nome_item;

                // Preenche a data inicial com a data de hoje
                const hoje = new Date().toISOString().split('T')[0]; // formato YYYY-MM-DD
                document.querySelector('input[name="data_inicial"]').value = hoje;

                document.getElementById('modalNaoConformidade').style.display = 'flex';
            }

            // Fechar modal
            document.getElementById('fecharModal').addEventListener('click', () => {
                document.getElementById('modalNaoConformidade').style.display = 'none';
            });


            // Atualiza data de conclusão com base na classificação
            const selectClassificacao = document.getElementById('classificacao_nc');
            const inputDataConclusao = document.getElementById('data_conclusao_nc');

            selectClassificacao.addEventListener('change', () => {
                const hoje = new Date();
                let dias = 0;

                switch(selectClassificacao.value) {
                    case 'leve':
                        dias = 7; 
                        break;
                    case 'moderado':
                        dias = 3;
                        break;
                    case 'urgente':
                        dias = 1;
                        break;
                    default:
                        dias = 0;
                }

                if(dias > 0){
                    const dataFinal = new Date(hoje.getTime() + dias * 24 * 60 * 60 * 1000);
                    inputDataConclusao.value = dataFinal.toISOString().split('T')[0];
                } else {
                    inputDataConclusao.value = '';
                }
            });

            // Salvar NC
            document.getElementById('salvarNC').addEventListener('click', () => {
            const formData = new FormData(document.getElementById('formNaoConformidade'));

            fetch('index.php?rota=salvar_nc', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(res => {
                if (res.trim() === 'ok') {
                    alert('Não conformidade registrada!');
                    document.getElementById('modalNaoConformidade').style.display = 'none';

                    // Atualizar contadores de NC dinamicamente
                    const classificacao = document.getElementById('classificacao_nc').value;
                    const spanId = 'cont-nc-' + classificacao;
                    const span = document.getElementById(spanId);
                    if (span) span.innerText = parseInt(span.innerText) + 1;

                    atualizarItensRestantes();
                } else {
                    alert('Erro ao salvar não conformidade.');
                }
                });
            });

        function atualizarItensRestantes() {
            const restantes = itens.filter(item => item.resultado_item === null || item.resultado_item === '').length;
            document.getElementById('itens-restantes').innerText = restantes;
        }
        </script>

        <!-- Resumo final -->
        <?php
            $itensAvaliaveis = array_filter($itens, fn($i) => ($i['resultado_item'] ?? '') !== 'nao_aplicavel');
            $itensCumpridos = array_filter($itensAvaliaveis, fn($i) => ($i['resultado_item'] ?? '') === 'conforme');

            $aderencia = count($itensAvaliaveis) > 0 ? round(count($itensCumpridos) / count($itensAvaliaveis) * 100, 1) : 0;
        ?>
        <div class="resumo">
            <p id="itens"><span id="itens-restantes"><?php echo count(array_filter($itens, fn($i) => empty($i['resultado_item']))); ?></span> itens restantes</p>
            <p id="aderencia"><?php echo $aderencia; ?>% de aderência</p>

            <a href="index.php?rota=listar_ncs&id_auditoria=<?php echo $idAuditoria; ?>">Finalizar Auditoria</a>
        </div>


</main>

</body>
</html>
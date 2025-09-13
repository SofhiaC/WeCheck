<?php
$idAuditoria = $_SESSION['id_auditoria'] ?? null;

if (!$idAuditoria) {
    header('Location: index.php?rota=auditorias');
    exit;
}

require_once __DIR__ . '/../controllers/ListarAuditoriaController.php';
require_once __DIR__ . '/../controllers/ChecklistController.php';

// Pegar dados da auditoria
$auditoria = ListarAuditoriaController::pegarAuditoria($idAuditoria);
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

        <div id="resultados">
            <p>Resultados</p>

            <img src="../assets/icons/ResultadosCumprem.png" alt="Icone de resultados">
            <div><span id="cont-conforme">0</span> Cumprem</div>
            <img src="../assets/icons/ResultadoNaoSeAplica.png" alt="Icone de resultados">
            <div><span id="cont-nao-aplicavel">0</span> Não se aplica</div>
            <img src="../assets/icons/ResultadoNaoCumpre.png" alt="Icone de resultados">
            <div><span id="cont-nao-conforme">0</span> Não se aplicam</div>
        </div>

        <div id="nfc">
            <p>NFCs</p>

            <img src="../assets/icons/NFCLeve.png" alt="Icone de NFCs">

            <img src="../assets/icons/NFCModerada.png" alt="Icone de NFCs">

            <img src="../assets/icons/NFCUrgente.png" alt="Icone de NFCs">

        </div> 

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
                    <td><?php echo $item['ordem_item']; ?></td>
                    <td><?php echo htmlspecialchars($item['nome_item']); ?></td>
                    <td id="resultado-<?php echo $item['id_item']; ?>">
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
            const itens = <?php echo json_encode($itens); ?>;

            // Abre o menu ao clicar no botão de resultado
            document.querySelectorAll('.btn-resultado').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    itemAtual = e.target.dataset.id;

                    // posiciona o menu próximo ao botão
                    menu.style.display = 'block';
                    menu.style.top = e.target.getBoundingClientRect().bottom + 'px';
                    menu.style.left = e.target.getBoundingClientRect().left + 'px';
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

                    // Envia para o servidor via POST
                    fetch('index.php?rota=atualizar_resultado', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `id_item=${itemAtual}&resultado=${valor}`
                    }).then(res => res.text())
                    .then(data => console.log(data));

                    // Fecha o menu
                    menu.style.display = 'none';
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
        </script>

        
        <div>
            <p>Itens restantes: <?php  ?></p>
            <p> <?php ?> de aderência</p>

            <a href="#">Finalizar Auditoria</a>
        </div>


</main>

</body>
</html>
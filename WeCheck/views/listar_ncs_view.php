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
    <link rel="stylesheet" href="#">
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
                <tr>
                    <td><?php echo (string) $nc['id_nc']; ?></td>
                    <td><?php echo htmlspecialchars($nc['nome_item'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($nc['nome_responsavel'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($nc['data_inicial'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($nc['data_conclusao'] ?? ''); ?></td>
                    <td>
                        <button>Ação</button> <!-- futuramente configurado -->
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>

<?php
$idAuditoria = $_SESSION['id_auditoria'] ?? null;

if (!$idAuditoria) {
    header('Location: index.php?rota=auditorias');
    exit;
}


require_once __DIR__ . '/../controllers/AuditoriaController.php';
require_once __DIR__ . '/../controllers/ChecklistController.php';

$idAuditoria = $_SESSION['id_auditoria'] ?? null;

if (!$idAuditoria) {
    header('Location: index.php?rota=auditorias');
    exit;
}

// Pegar dados da auditoria
$auditoria = AuditoriaController::pegarAuditoria($idAuditoria);
if (!$auditoria) {
    die("Auditoria não encontrada.");
}

// Pegar itens do checklist
$itens = ChecklistController::listarItens($idAuditoria);

// Inclui a view
require_once __DIR__ . '/../views/checklist_view.php';
?> 


<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/home.css">
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

        <a href="#">Adicionar Responsável</a>
        <a href="#">Vizualizar Responsáveis</a>

        <h3>Liste os itens do CheckList </h3>

        <div>
            <img src="../assets/icons/AdicaoItem.png" alt="Icone de adição">
            <a href="#">Adicionar Item</a>
        </div>

        <div>
            <?php foreach ($itens as $item): ?>
                <li>
                    #<?php echo $item['ordem_item']; ?> 
                    (Banco: <?php echo $item['id_item']; ?>) - 
                    <?php echo htmlspecialchars($item['nome_item']); ?>
                </li>
            <?php endforeach; ?>

        </div>

        <div>
            <?php 

                echo htmlspecialchars(count($itens)) . " itens no checklist.";
            ?>
            <a href="#">Iniciar Auditoria</a>
        </div>

    </main>

    <footer>
        <p>© 2025 WeCheck. Por Midup.</p>
    </footer>
    
</body>
</html>
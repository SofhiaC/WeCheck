<?php
// auditorias_view.php - REMOVA session_start() e a conexão duplicada
$idUsuario = $_SESSION['id_usuario'] ?? null;

if (!$idUsuario) {
    header('Location: index.php?rota=login');
    exit;
}
?> 

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/auditorias.css">
    <title>WeCheck</title>
</head>
<body>
    <header>
        <img src="../assets/logo/WeCheck_Logo.png" alt="Logo WeCheck"> 
        <img src="../assets/logo/WeCheck_Escrita.png" alt="Nome WeCheck">

        <a href="index.php?rota=home">Início</a>
        <a href="#">Conta</a>
    </header>

    <main>
        <h1>Selecione o seu projeto</h1>
        <p>Ou crie um novo para auditar.</p>

        <div class="novo-projeto">
            <img src="../assets/icons/CriarProjeto.png" alt="Ícone de adicionar">
            <a href="index.php?rota=auditoria_criacao">Novo Projeto</a>
            <p>Faça upload dos seus arquivos de requisitos para iniciar uma nova auditoria.</p>
        </div>

        <div class="lista-projetos">
            <?php if (!empty($auditorias)): ?>
                <?php foreach ($auditorias as $auditoria): ?>
                    <div class="projeto">
                        <h2><?= htmlspecialchars($auditoria['nome_auditoria']) ?></h2>
                        <p>Empresa: <?= htmlspecialchars($auditoria['empresa_auditoria']) ?></p>
                        <a href="index.php?rota=auditoria&id=<?= $auditoria['id_auditoria'] ?>">Abrir Auditoria</a>
                        <p>Data de criação: <?= htmlspecialchars($auditoria['data_criacao']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Você ainda não possui auditorias cadastradas.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>© 2025 WeCheck. Por Midup.</p>
    </footer>
</body>
</html>
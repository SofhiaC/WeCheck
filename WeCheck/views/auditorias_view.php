<?php
    session_start();
    $idUsuario = $_SESSION['id_usuario'] ?? null;

    if (!$idUsuario) {
        header('Location: index.php?rota=login'); // ou tela inicial
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

        <a href="#">Conta</a>
    </header>

    <main>
        <h1>Selecione o seu projeto</h1>
        <p>Ou crie um novo para auditar.</p>

        <div class="novo-projeto">
            <img src="../assets/icons/CriarProjeto.png" alt="Ícone de adicionar">
            <a href="#">Novo Projeto</a>
            <p>Faça upload dos seus arquivos de requisitos para iniciar uma nova auditoria.</p>
        </div>


        <div class="lista-projetos">
        <?php if (!empty($auditorias)): ?>
            <?php foreach ($auditorias as $auditoria): ?>
                <div class="projeto">
                    <h2><?= htmlspecialchars($auditoria['nome_auditoria']) ?></h2>
                    <p><?= htmlspecialchars($auditoria['empresa_auditoria']) ?></p>
                    <?php if (!empty($auditoria['documento_pdf'])): ?>
                        <a href="../uploads/<?= htmlspecialchars($auditoria['documento_pdf']) ?>" target="_blank">Ver Documento</a>
                    <?php endif; ?>
                    <a href="router.php?rota=auditoria&id=<?= $auditoria['id_auditoria'] ?>">Abrir Auditoria</a>
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
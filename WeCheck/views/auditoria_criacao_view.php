<?php
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
    <link rel="stylesheet" href="../assets/css/auditoriacriacao.css">
    <title>WeCheck</title>
</head>
<body>
    <header>
        <img src="../assets/logo/WeCheck_Logo.png" alt="Logo WeCheck"> 
        <img src="../assets/logo/WeCheck_Escrita.png" alt="Nome WeCheck">

        <a href="#">Início</a>
        <a href="#">Conta</a>
    </header>

    <main>
        <h1>Criando nova auditoria</h1>
        
        <form action="index.php?rota=criar_auditoria" method="POST" enctype="multipart/form-data">
            <label>
                Nome do projeto
                <input type="text" name="nome_auditoria" placeholder="Nome do projeto auditado" required>
            </label>
            <label>
                Empresa
                <input type="text" name="empresa_auditoria" placeholder="Nome da Empresa" required>
            </label>
            <label>
                Documento de requisitos (PDF)
                <input type="file" name="documento_pdf" accept="application/pdf" required>
            </label>

            <button type="submit">Iniciar CheckList</button>
        </form>
    
        

    </main>

    <footer>
        <p>© 2025 WeCheck. Por Midup.</p>
    </footer>
    
</body>
</html>
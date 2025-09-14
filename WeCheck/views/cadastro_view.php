
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/cadastrando.css">
    <title>WeCheck</title>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../assets/logo/WeCheck_Logo.png" alt="Logo WeCheck"> 
            <img src="../assets/logo/WeCheck_Escrita.png" alt="Nome WeCheck">
        </div>
        <div class="botoes-nav">
            <a id="entrar" href="#">Entrar</a>
        </div>
    </header>

    <main>                                          
        <h1>Crie sua conta</h1>
        <p>Faça seu cadastro para conhecer mais da WeCheck e como ela pode te auxiliar em processos de auditoria.</p>

        <form action="index.php?rota=cadastro" method="POST">
            <label>
                Nome Completo
                <input type="text" name="nome_usuario" placeholder="Nome Completo" required>
            </label>
            <label>
                E-mail
                <input type="email" name="email_usuario" placeholder="Email" required>
            </label>
            <label>
                Senha
                <input type="password" name="senha_usuario" placeholder="Senha" required>
            </label>

            <button type="submit">Cadastrar</button>
        </form>
    </main>

    <footer>
        <p>© 2025 WeCheck. Por Midup.</p>
    </footer>
    
</body>
</html>
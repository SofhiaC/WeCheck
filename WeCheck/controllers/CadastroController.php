<?php
class CadastroController {
    public static function cadastrar() {
        $pdo = Database::getConnection(); // Conexão única

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome_usuario'] ?? '';
            $email = $_POST['email_usuario'] ?? '';
            $senha = $_POST['senha_usuario'] ?? '';


            $stmt = $pdo->prepare("INSERT INTO tb_usuario (nome_usuario, email_usuario, senha_usuario) VALUES (?, ?, ?)");
            if ($stmt->execute([$nome, $email, $senha])) {
                $_SESSION['id_usuario'] = $pdo->lastInsertId();
                header('Location: index.php?rota=auditorias');
                exit;
            } else {
                $errorInfo = $stmt->errorInfo();
                echo "Erro ao cadastrar: " . $errorInfo[2];
            }
        } else {
            require __DIR__ . "/../views/cadastro_view.php";
        }
    }
}
?>
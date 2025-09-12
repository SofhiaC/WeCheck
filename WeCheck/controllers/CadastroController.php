<?php
class CadastroController {
    public static function cadastrar() {
        require __DIR__ . "/../config/db.php";
        $pdo = Database::getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome_usuario'] ?? '';
            $email = $_POST['email_usuario'] ?? '';
            $senha = $_POST['senha_usuario'] ?? '';

            $stmt = $pdo->prepare("INSERT INTO tb_usuario (nome_usuario, email_usuario, senha_usuario) VALUES (?, ?, ?)");
            if ($stmt->execute([$nome, $email, $senha])) {
                session_start();
                $_SESSION['id_usuario'] = $pdo->lastInsertId(); // vai pegar o id que foi recem cadastrado
                    header('Location: ../views/auditorias_view.php');
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




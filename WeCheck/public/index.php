<?php
require_once __DIR__ . '/../controllers/CadastroController.php';

$rota = $_GET['rota'] ?? 'home';

switch ($rota) {
    case 'cadastro':
        CadastroController::cadastrar(); // Ele inclui cadastro_view.php dentro do controller
        break;

    case 'home':
    default:
        require_once __DIR__ . '/../views/home_view.php';
        break;
}
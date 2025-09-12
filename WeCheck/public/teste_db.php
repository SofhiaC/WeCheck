<?php
require_once '../config/db.php';

$conn = Database::getConnection();

if ($conn) {
    echo "✅ Conexão realizada com sucesso!";
}
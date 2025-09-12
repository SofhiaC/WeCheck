<?php
class Database {
    private static $host = "127.0.0.1";   // ou localhost
    private static $port = "3306";        // porta do Laragon
    private static $db_name = "wecheck"; // seu banco
    private static $username = "root";    // padrão do Laragon
    private static $password = "";        // senha padrão do root no Laragon é vazia
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$db_name,
                    self::$username,
                    self::$password,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro na conexão: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>
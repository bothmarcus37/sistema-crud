<?php
// =========================================================
// CONEXIÓN A MYSQL
// Cambia estos datos si tu MySQL usa otro usuario o contraseña
// (en XAMPP recién instalado el usuario es "root" y la contraseña va vacía).
// =========================================================
$host     = "localhost";
$user     = "root";
$password = "root";
$dbname   = "gestion_web";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $password);
    $conn->set_charset("utf8mb4");

    // Si la base o la tabla no existen, se crean solas la primera vez.
    $conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db($dbname);
    $conn->query("
        CREATE TABLE IF NOT EXISTS clientes (
            id_cliente    INT AUTO_INCREMENT PRIMARY KEY,
            nombre        VARCHAR(100) NOT NULL,
            correo        VARCHAR(100) NOT NULL,
            tipo_servicio VARCHAR(50)  NOT NULL,
            estado        VARCHAR(20)  DEFAULT 'Pendiente'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    // No se muestra el error técnico al visitante; queda en el registro de PHP.
    error_log("Registro de clientes: " . $e->getMessage());
    die("<p style=\"font-family:sans-serif;max-width:40em;margin:3em auto\">No se pudo conectar con MySQL. "
      . "Revisa que MySQL esté encendido y que el usuario y la contraseña de <code>conexion.php</code> sean los correctos.</p>");
}

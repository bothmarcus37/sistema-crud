<?php
// =========================================================
// CONEXIÓN A MYSQL
// Datos para tu equipo (XAMPP). En XAMPP recién instalado el usuario
// es "root" y la contraseña va vacía.
//
// Para publicar en un hosting NO cambies este archivo: crea ahí
// local.php (copia de local.example.php) con los datos que te da
// el hosting. Ese archivo no se sube a GitHub.
// =========================================================
$config = [
    "host"     => "localhost",
    "user"     => "root",
    "password" => "root",
    "dbname"   => "gestion_web",
];

if (is_file(__DIR__ . "/local.php")) {
    $config = array_merge($config, require __DIR__ . "/local.php");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($config["host"], $config["user"], $config["password"]);
    $conn->set_charset("utf8mb4");

    $dbname = str_replace("`", "", $config["dbname"]);

    // En tu equipo la base se crea sola. En un hosting compartido no hay
    // permiso para crearla (se crea desde su panel), así que el error se ignora.
    try {
        $conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    } catch (mysqli_sql_exception $e) {
        // sigue: la base ya debe existir
    }
    $conn->select_db($dbname);

    // La tabla se crea sola la primera vez.
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
      . "Revisa que MySQL esté encendido y que el usuario y la contraseña de <code>conexion.php</code> "
      . "(o <code>local.php</code> en el hosting) sean los correctos.</p>");
}

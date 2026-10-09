<?php
require "conexion.php";
require "funciones.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

[$datos, $errores] = leerCliente($_POST);

if ($errores) {
    // Se regresa al formulario con los errores y lo que ya se había escrito.
    $_SESSION["errores"] = $errores;
    $_SESSION["anterior"] = $datos;
    header("Location: index.php");
    exit;
}

// Consulta preparada: los datos viajan separados del SQL (evita inyección SQL).
$stmt = $conn->prepare("INSERT INTO clientes (nombre, correo, tipo_servicio, estado) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $datos["nombre"], $datos["correo"], $datos["tipo_servicio"], $datos["estado"]);
$stmt->execute();

$_SESSION["aviso"] = "Cliente guardado: " . $datos["nombre"] . ".";
header("Location: index.php");
exit;

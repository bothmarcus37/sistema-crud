<?php
require "conexion.php";
require "funciones.php";
session_start();

// Solo por POST: así un enlace o una imagen no pueden borrar un cliente.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = (int) ($_POST["id"] ?? 0);

$stmt = $conn->prepare("DELETE FROM clientes WHERE id_cliente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$_SESSION["aviso"] = $stmt->affected_rows > 0 ? "Cliente eliminado." : "Ese cliente ya no existía.";
header("Location: index.php");
exit;

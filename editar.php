<?php
require "conexion.php";
require "funciones.php";
session_start();

$id = (int) ($_GET["id"] ?? $_POST["id"] ?? 0);

$stmt = $conn->prepare("SELECT id_cliente, nombre, correo, tipo_servicio, estado FROM clientes WHERE id_cliente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();

if (!$cliente) {
    $_SESSION["aviso"] = "Ese cliente ya no existe.";
    header("Location: index.php");
    exit;
}

$errores = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    [$datos, $errores] = leerCliente($_POST);

    if (!$errores) {
        $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, correo = ?, tipo_servicio = ?, estado = ? WHERE id_cliente = ?");
        $stmt->bind_param("ssssi", $datos["nombre"], $datos["correo"], $datos["tipo_servicio"], $datos["estado"], $id);
        $stmt->execute();

        $_SESSION["aviso"] = "Cliente actualizado: " . $datos["nombre"] . ".";
        header("Location: index.php");
        exit;
    }
    $cliente = array_merge($cliente, $datos); // conserva lo que se escribió
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar cliente · Registro de clientes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="cabecera">
        <div class="contenedor">
            <h1>Editar cliente</h1>
            <p>Cliente #<?= (int) $cliente["id_cliente"] ?></p>
        </div>
    </header>

    <main class="contenedor contenedor--angosto">
        <section class="panel">
            <?php if ($errores): ?>
                <ul class="errores" role="alert">
                    <?php foreach ($errores as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form action="editar.php" method="POST" id="formCliente" novalidate>
                <input type="hidden" name="id" value="<?= (int) $cliente["id_cliente"] ?>">

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" maxlength="100" required value="<?= e($cliente["nombre"]) ?>">

                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" maxlength="100" required value="<?= e($cliente["correo"]) ?>">

                <label for="tipo_servicio">Servicio</label>
                <select id="tipo_servicio" name="tipo_servicio" required>
                    <?= opciones(SERVICIOS, $cliente["tipo_servicio"]) ?>
                </select>

                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <?= opciones(ESTADOS, $cliente["estado"]) ?>
                </select>

                <p class="error-campo" id="errorFormulario" hidden></p>
                <div class="fila-botones">
                    <button type="submit" class="boton">Guardar cambios</button>
                    <a href="index.php" class="boton boton--claro">Cancelar</a>
                </div>
            </form>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>

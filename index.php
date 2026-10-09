<?php
require "conexion.php";
require "funciones.php";
session_start();

// Mensaje de una sola vez (cliente guardado, actualizado o eliminado)
$aviso = $_SESSION["aviso"] ?? null;
$errores = $_SESSION["errores"] ?? [];
$anterior = $_SESSION["anterior"] ?? [];
unset($_SESSION["aviso"], $_SESSION["errores"], $_SESSION["anterior"]);

// Filtros de la tabla
$buscar = trim($_GET["buscar"] ?? "");
$filtroEstado = in_array($_GET["estado"] ?? "", ESTADOS, true) ? $_GET["estado"] : "";

$sql = "SELECT id_cliente, nombre, correo, tipo_servicio, estado FROM clientes WHERE 1=1";
$tipos = "";
$valores = [];
if ($buscar !== "") {
    $sql .= " AND (nombre LIKE ? OR correo LIKE ?)";
    $tipos .= "ss";
    $valores[] = "%$buscar%";
    $valores[] = "%$buscar%";
}
if ($filtroEstado !== "") {
    $sql .= " AND estado = ?";
    $tipos .= "s";
    $valores[] = $filtroEstado;
}
$sql .= " ORDER BY id_cliente DESC";

$stmt = $conn->prepare($sql);
if ($tipos !== "") {
    $stmt->bind_param($tipos, ...$valores);
}
$stmt->execute();
$clientes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Totales por estado para el resumen
$totales = array_fill_keys(ESTADOS, 0);
$res = $conn->query("SELECT estado, COUNT(*) AS n FROM clientes GROUP BY estado");
while ($fila = $res->fetch_assoc()) {
    if (isset($totales[$fila["estado"]])) {
        $totales[$fila["estado"]] = (int) $fila["n"];
    }
}
$totalClientes = array_sum($totales);
$hayFiltros = $buscar !== "" || $filtroEstado !== "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de clientes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="cabecera">
        <div class="contenedor">
            <h1>Registro de clientes</h1>
            <p>Alta, consulta, edición y baja de los clientes de un servicio web.</p>
        </div>
    </header>

    <main class="contenedor">
        <?php if ($aviso): ?>
            <p class="aviso" role="status"><?= e($aviso) ?></p>
        <?php endif; ?>

        <section class="resumen" aria-label="Resumen">
            <div class="resumen__dato"><strong><?= $totalClientes ?></strong><span>Clientes</span></div>
            <?php foreach ($totales as $estado => $n): ?>
                <div class="resumen__dato"><strong><?= $n ?></strong><span><?= e($estado) ?></span></div>
            <?php endforeach; ?>
        </section>

        <div class="columnas">
            <section class="panel">
                <h2>Nuevo cliente</h2>

                <?php if ($errores): ?>
                    <ul class="errores" role="alert">
                        <?php foreach ($errores as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form action="insertar.php" method="POST" id="formCliente" novalidate>
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" required
                           value="<?= e($anterior["nombre"] ?? "") ?>">

                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" maxlength="100" required
                           placeholder="nombre@dominio.com" value="<?= e($anterior["correo"] ?? "") ?>">

                    <label for="tipo_servicio">Servicio</label>
                    <select id="tipo_servicio" name="tipo_servicio" required>
                        <option value="">Selecciona un servicio</option>
                        <?= opciones(SERVICIOS, $anterior["tipo_servicio"] ?? "") ?>
                    </select>

                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        <?= opciones(ESTADOS, $anterior["estado"] ?? "Pendiente") ?>
                    </select>

                    <p class="error-campo" id="errorFormulario" hidden></p>
                    <button type="submit" class="boton">Guardar cliente</button>
                </form>
            </section>

            <section class="panel panel--tabla">
                <div class="panel__encabezado">
                    <h2>Clientes registrados</h2>
                    <form method="GET" class="filtros">
                        <input type="search" name="buscar" placeholder="Buscar por nombre o correo"
                               aria-label="Buscar por nombre o correo" value="<?= e($buscar) ?>">
                        <select name="estado" aria-label="Filtrar por estado">
                            <option value="">Todos los estados</option>
                            <?= opciones(ESTADOS, $filtroEstado) ?>
                        </select>
                        <button type="submit" class="boton boton--claro">Filtrar</button>
                        <?php if ($hayFiltros): ?><a href="index.php" class="enlace">Quitar filtros</a><?php endif; ?>
                    </form>
                </div>

                <?php if (!$clientes): ?>
                    <p class="vacio">
                        <?= $hayFiltros
                            ? "Ningún cliente coincide con esos filtros."
                            : "Todavía no hay clientes. Registra el primero con el formulario." ?>
                    </p>
                <?php else: ?>
                    <div class="tabla-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Servicio</th>
                                    <th>Estado</th>
                                    <th><span class="oculto">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes as $c): ?>
                                    <tr>
                                        <td><?= (int) $c["id_cliente"] ?></td>
                                        <td><?= e($c["nombre"]) ?></td>
                                        <td><a href="mailto:<?= e($c["correo"]) ?>" class="enlace"><?= e($c["correo"]) ?></a></td>
                                        <td><?= e($c["tipo_servicio"]) ?></td>
                                        <td><span class="estado <?= claseEstado($c["estado"]) ?>"><?= e($c["estado"]) ?></span></td>
                                        <td class="acciones">
                                            <a href="editar.php?id=<?= (int) $c["id_cliente"] ?>" class="boton boton--claro boton--chico">Editar</a>
                                            <form action="eliminar.php" method="POST" class="form-eliminar">
                                                <input type="hidden" name="id" value="<?= (int) $c["id_cliente"] ?>">
                                                <button type="submit" class="boton boton--peligro boton--chico">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>

<?php
// Listas válidas y funciones que comparten todas las páginas.

const SERVICIOS = ["Sitio Web Responsivo", "Panel Administrativo", "Mantenimiento"];
const ESTADOS   = ["Pendiente", "En Desarrollo", "Finalizado"];

// Escapa texto antes de imprimirlo en HTML (evita que un dato guardado inyecte código).
function e($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
}

// Lee y valida los campos del formulario. Devuelve [datos, errores].
function leerCliente(array $post) {
    $datos = [
        "nombre"        => trim($post["nombre"] ?? ""),
        "correo"        => trim($post["correo"] ?? ""),
        "tipo_servicio" => $post["tipo_servicio"] ?? "",
        "estado"        => $post["estado"] ?? "",
    ];
    $errores = [];

    if ($datos["nombre"] === "" || mb_strlen($datos["nombre"]) > 100) {
        $errores[] = "Escribe el nombre del cliente (máximo 100 caracteres).";
    }
    if (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL) || mb_strlen($datos["correo"]) > 100) {
        $errores[] = "Escribe un correo válido, por ejemplo nombre@dominio.com.";
    }
    if (!in_array($datos["tipo_servicio"], SERVICIOS, true)) {
        $errores[] = "Selecciona un servicio de la lista.";
    }
    if (!in_array($datos["estado"], ESTADOS, true)) {
        $errores[] = "Selecciona un estado de la lista.";
    }
    return [$datos, $errores];
}

// Clase CSS de la etiqueta de color según el estado.
function claseEstado($estado) {
    return [
        "Pendiente"     => "estado--pendiente",
        "En Desarrollo" => "estado--desarrollo",
        "Finalizado"    => "estado--finalizado",
    ][$estado] ?? "estado--pendiente";
}

// Dibuja las <option> de un <select> marcando la elegida.
function opciones(array $lista, $elegida) {
    $html = "";
    foreach ($lista as $valor) {
        $marcada = $valor === $elegida ? " selected" : "";
        $html .= "<option value=\"" . e($valor) . "\"$marcada>" . e($valor) . "</option>";
    }
    return $html;
}

-- Base de datos del Registro de clientes.
-- No hace falta importarla a mano: conexion.php la crea sola la primera vez.
-- Se deja aquí como referencia o para crearla desde phpMyAdmin.

CREATE DATABASE IF NOT EXISTS gestion_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_web;

CREATE TABLE IF NOT EXISTS clientes (
    id_cliente    INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(100) NOT NULL,
    correo        VARCHAR(100) NOT NULL,
    tipo_servicio VARCHAR(50)  NOT NULL,
    estado        VARCHAR(20)  DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

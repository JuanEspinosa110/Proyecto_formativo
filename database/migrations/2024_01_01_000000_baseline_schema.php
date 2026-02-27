<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Recreates the baseline structure of the project.
     * Note: This reflects the legacy database state before the 2026 refactor.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $statements = [
            // 1. Independent Lookup Tables
            "CREATE TABLE `estado` (
              `id_estado` tinyint(3) unsigned NOT NULL,
              `nombre_estado` varchar(50) DEFAULT NULL,
              `descripcion` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`id_estado`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `departamento` (
              `id_departamento` char(2) NOT NULL,
              `nombre_departamento` varchar(100) NOT NULL,
              PRIMARY KEY (`id_departamento`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `ciudad` (
              `id_ciudad` char(6) NOT NULL,
              `nombre_city` varchar(100) NOT NULL,
              `id_departamento` char(2) NOT NULL,
              PRIMARY KEY (`id_ciudad`),
              FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `barrio` (
              `id_barrio` int(11) NOT NULL AUTO_INCREMENT,
              `nombre` varchar(100) NOT NULL,
              `id_ciudad` char(6) NOT NULL,
              PRIMARY KEY (`id_barrio`),
              CONSTRAINT `barrio_fk_ciudad` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudad` (`id_ciudad`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `tipo_empresa` (
              `id_tipo_empresa` tinyint(4) NOT NULL,
              `nombre_tipo` varchar(50) NOT NULL,
              PRIMARY KEY (`id_tipo_empresa`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `tipo_usuario` (
              `id_tipo_usuario` tinyint(4) NOT NULL,
              `nombre_tipo` varchar(50) NOT NULL,
              PRIMARY KEY (`id_tipo_usuario`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `tipo_documento` (
              `id_tipo_documento` int(11) NOT NULL,
              `nombre_tipo` varchar(100) NOT NULL,
              PRIMARY KEY (`id_tipo_documento`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            // 2. Core Tables
            "CREATE TABLE `empresa` (
              `NIT` bigint(15) unsigned NOT NULL,
              `nombre_empresa` varchar(150) NOT NULL,
              `doc_representante` bigint(20) unsigned NOT NULL,
              `primer_nombre_repre` varchar(50) NOT NULL,
              `segundo_nombre_repre` varchar(50) DEFAULT NULL,
              `primer_apellido_repre` varchar(50) NOT NULL,
              `segundo_apellido_repre` varchar(50) DEFAULT NULL,
              `telefono_representante` varchar(20) DEFAULT NULL,
              `correo_representante` varchar(150) DEFAULT NULL,
              `telefono_empresa` varchar(20) DEFAULT NULL,
              `correo_corporativo` varchar(150) DEFAULT NULL,
              `id_tipo_empresa` tinyint(4) NOT NULL,
              `id_ciudad` char(6) DEFAULT NULL,
              `id_estado` tinyint(3) unsigned DEFAULT NULL,
              `fecha_creacion` datetime DEFAULT current_timestamp(),
              PRIMARY KEY (`NIT`),
              CONSTRAINT `empresa_fk_estado` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
              CONSTRAINT `empresa_fk_tipo` FOREIGN KEY (`id_tipo_empresa`) REFERENCES `tipo_empresa` (`id_tipo_empresa`),
              CONSTRAINT `empresa_fk_ciudad` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudad` (`id_ciudad`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `usuario` (
              `doc_usuario` bigint(20) unsigned NOT NULL,
              `NIT` bigint(15) unsigned DEFAULT NULL,
              `primer_nombre` varchar(50) DEFAULT NULL,
              `segundo_nombre` varchar(50) DEFAULT NULL,
              `primer_apellido` varchar(50) DEFAULT NULL,
              `segundo_apellido` varchar(50) DEFAULT NULL,
              `correo` varchar(150) DEFAULT NULL,
              `password` varchar(255) DEFAULT NULL,
              `telefono` varchar(20) DEFAULT NULL,
              `foto_usuario` varchar(300) DEFAULT NULL,
              `id_tipo_usuario` tinyint(4) DEFAULT NULL,
              `id_ciudad` char(6) DEFAULT NULL,
              `id_estado` tinyint(3) unsigned DEFAULT NULL,
              PRIMARY KEY (`doc_usuario`),
              CONSTRAINT `usuario_fk_tipo` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipo_usuario` (`id_tipo_usuario`),
              CONSTRAINT `usuario_fk_estado` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
              CONSTRAINT `usuario_fk_empresa` FOREIGN KEY (`NIT`) REFERENCES `empresa` (`NIT`),
              CONSTRAINT `usuario_fk_ciudad` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudad` (`id_ciudad`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `bus` (
              `placa` varchar(15) NOT NULL,
              `NIT` bigint(15) unsigned DEFAULT NULL,
              `modelo` varchar(50) DEFAULT NULL,
              `capacidad_pasajeros` int(11) DEFAULT NULL,
              `kilometraje` int(11) DEFAULT NULL,
              `id_estado` tinyint(3) unsigned DEFAULT NULL,
              `linc_transito` bigint(11) DEFAULT NULL,
              `numero_chasis` varchar(17) DEFAULT NULL,
              `numero_motro` varchar(14) DEFAULT NULL,
              `doc_propietario` bigint(12) DEFAULT NULL,
              `nombre_propietario` varchar(50) DEFAULT NULL,
              `telefono` varchar(20) DEFAULT NULL,
              `correo` varchar(150) DEFAULT NULL,
              PRIMARY KEY (`placa`),
              CONSTRAINT `bus_fk_estado` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
              CONSTRAINT `bus_fk_empresa` FOREIGN KEY (`NIT`) REFERENCES `empresa` (`NIT`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `ruta` (
              `id_ruta` int(11) NOT NULL AUTO_INCREMENT,
              `id_ciudad` char(6) NOT NULL,
              `id_barrio_origen` int(11) NOT NULL,
              `origen` varchar(150) NOT NULL,
              `id_barrio_destino` int(11) NOT NULL,
              `destino` varchar(150) NOT NULL,
              `id_estado` tinyint(3) unsigned NOT NULL,
              PRIMARY KEY (`id_ruta`),
              CONSTRAINT `ruta_fk_estado` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
              CONSTRAINT `ruta_fk_ciudad` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudad` (`id_ciudad`),
              CONSTRAINT `ruta_fk_barrio_orig` FOREIGN KEY (`id_barrio_origen`) REFERENCES `barrio` (`id_barrio`),
              CONSTRAINT `ruta_fk_barrio_dest` FOREIGN KEY (`id_barrio_destino`) REFERENCES `barrio` (`id_barrio`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `viaje` (
              `id_viaje` int(11) NOT NULL AUTO_INCREMENT,
              `placa` varchar(15) DEFAULT NULL,
              `id_ruta` int(11) DEFAULT NULL,
              `doc_us` bigint(20) unsigned DEFAULT NULL,
              `fecha` datetime DEFAULT NULL,
              `id_estado` tinyint(3) unsigned DEFAULT NULL,
              PRIMARY KEY (`id_viaje`),
              CONSTRAINT `viaje_fk_bus` FOREIGN KEY (`placa`) REFERENCES `bus` (`placa`),
              CONSTRAINT `viaje_fk_ruta` FOREIGN KEY (`id_ruta`) REFERENCES `ruta` (`id_ruta`),
              CONSTRAINT `viaje_fk_user` FOREIGN KEY (`doc_us`) REFERENCES `usuario` (`doc_usuario`),
              CONSTRAINT `viaje_fk_estado` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE `documentos` (
              `id_documento` bigint(20) NOT NULL AUTO_INCREMENT,
              `nombre` varchar(150) NOT NULL,
              `archivo` varchar(255) NOT NULL,
              `fecha_expedicion` date NOT NULL,
              `fecha_vencimiento` date NOT NULL,
              `id_tipo_documento` int(11) NOT NULL,
              `doc_usuario` bigint(20) unsigned DEFAULT NULL,
              `NIT` bigint(15) unsigned DEFAULT NULL,
              `placa` varchar(15) DEFAULT NULL,
              `id_estado` tinyint(4) NOT NULL,
              `created_at` timestamp NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id_documento`),
              CONSTRAINT `doc_fk_tipo` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipo_documento` (`id_tipo_documento`),
              CONSTRAINT `doc_fk_usuario` FOREIGN KEY (`doc_usuario`) REFERENCES `usuario` (`doc_usuario`),
              CONSTRAINT `doc_fk_empresa` FOREIGN KEY (`NIT`) REFERENCES `empresa` (`NIT`),
              CONSTRAINT `doc_fk_bus` FOREIGN KEY (`placa`) REFERENCES `bus` (`placa`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        ];

        foreach ($statements as $sql) {
            DB::statement($sql);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        $tables = [
            'documentos', 'viaje', 'ruta', 'bus', 'usuario', 'empresa', 
            'tipo_documento', 'tipo_usuario', 'tipo_empresa', 'barrio', 
            'ciudad', 'departamento', 'estado'
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();
    }
};

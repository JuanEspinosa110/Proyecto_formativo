<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EssentialDataSeeder extends Seeder
{
    /**
     * Run the database seeds for essential project data only.
     * This seeder ignores operational data (buses, trips, etc.) and focusing on
     * the foundations required for the system to boot and the SuperAdmin to work.
     */
    public function run(): void
    {
        $this->command->info('Iniciando carga de datos esenciales...');

        $this->call([
            // 1. Estados (Base de todo el sistema)
            InitialDataSeeder::class,

            // 2. Ubicaciones (Geografía necesaria para empresas y rutas)
            DepartamentoSeeder::class,
            CiudadSeeder::class,
            BarrioSeeder::class,

            // 3. Catálogos de Tipos (Clasificaciones necesarias)
            TipoDocumentoSeeder::class,
            TipoEmpresaSeeder::class,
            TipoUsuarioSeeder::class,

            // 4. Configuración Administrativa y Operativa Base
            PlanesLicenciaSeeder::class,
            SuperAdministradorSeeder::class, // Incluye a Cesar Esquivel
            RutaSeeder::class,               // Rutas base del sistema
        ]);

        $this->command->info('¡Datos esenciales cargados con éxito!');
        $this->command->warn('Nota: Esta carga no incluye buses, conductores ni viajes operativos.');
    }
}

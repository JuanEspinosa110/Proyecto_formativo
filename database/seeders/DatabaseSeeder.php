<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            InitialDataSeeder::class,
            DepartamentoSeeder::class,
            CiudadSeeder::class,
            BarrioSeeder::class,
            TipoDocumentoSeeder::class,
            TipoEmpresaSeeder::class,
            TipoUsuarioSeeder::class,
            EmpresaSeeder::class,
            UsuarioEmpresaSeeder::class,
            LicenciaEmpresaSeeder::class,
            SuperAdministradorSeeder::class,
            BusSeeder::class,
            GestorSetpSeeder::class,
            RutaSeeder::class,
            DocumentoSeeder::class,
            PasajeroSeeder::class,
            AfiliacionSeeder::class,
            RecargaSeeder::class,
            AsignacionSeeder::class,
            RecorridoSeeder::class,
            ViajeSeeder::class,
            NovedadRecorridoSeeder::class,
            MantenimientoSeeder::class,
            ReporteFallasSeeder::class,
            GastosSeeder::class,
        ]);

    }
}

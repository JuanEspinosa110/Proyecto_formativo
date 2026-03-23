<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Empresa;
use App\Models\Usuario;

class EmpresaRecargasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Crear Empresa de Recargas
        $nitEmpresaRecarga = 900555666;
        
        $empresaExiste = Empresa::where('NIT', $nitEmpresaRecarga)->exists();

        if (!$empresaExiste) {
            Empresa::create([
                'NIT' => $nitEmpresaRecarga,
                'nombre_empresa' => 'RECARGAS GANA GANA S.A.',
                'doc_representante' => 987654321,
                'primer_nombre_repre' => 'JUAN',
                'primer_apellido_repre' => 'PEREZ',
                'id_tipo_empresa' => 6, // EMPRESA DE RECARGAS
                'id_ciudad' => '730001', // Asumiendo que 730001 (Ibague) existe
                'id_estado' => 1,
            ]);
        }

        // 2. Crear Gestor de Recargas (Usuario)
        $docUsuario = 1111111111;

        $usuarioExiste = Usuario::where('doc_usuario', $docUsuario)->exists();

        if (!$usuarioExiste) {
            Usuario::create([
                'doc_usuario' => $docUsuario,
                'NIT' => $nitEmpresaRecarga,
                'id_tipo_usuario' => 10, // GESTOR RECARGAS
                'primer_nombre' => 'RICARDO',
                'primer_apellido' => 'GOMEZ',
                'correo' => 'gestor@recargas.com',
                'password' => Hash::make('password123'),
                'id_ciudad' => '730001',
                'id_estado' => 1,
                'telefono' => '3001234567',
            ]);
        }
    }
}

<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioEmpresaSeeder extends Seeder
{
    public function run()
    {
        // --- Empresa 1: TRANSPORTE TEST SAS (900123456) ---
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 1000000001,
                'NIT' => 900123456,
                'primer_nombre' => 'REPRE',
                'segundo_nombre' => 'PRUEBA',
                'primer_apellido' => 'TEST',
                'segundo_apellido' => 'EMPRESA',
                'correo' => 'admin@transportetest.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3001234567',
                'fecha_nacimiento' => '1980-01-01',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1, // ADMINISTRADOR
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 1000000002,
                'NIT' => 900123456,
                'primer_nombre' => 'MATEO',
                'segundo_nombre' => 'ANDRES',
                'primer_apellido' => 'LOPEZ',
                'segundo_apellido' => 'GARCIA',
                'correo' => 'mlopez@transportetest.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3101112233',
                'fecha_nacimiento' => '1985-05-15',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 1000000003,
                'NIT' => 900123456,
                'primer_nombre' => 'VALENTINA',
                'segundo_nombre' => 'MARIA',
                'primer_apellido' => 'RUIZ',
                'segundo_apellido' => 'SALAZAR',
                'correo' => 'vruiz@transportetest.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3112223344',
                'fecha_nacimiento' => '1990-10-20',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ]
        ]);


        // --- PROPIETARIOS ---

        // Empresa 1: TRANSPORTE TEST SAS (900123456)
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 2000000001,
                'NIT' => 900123456,
                'primer_nombre' => 'CARLOS',
                'segundo_nombre' => 'ALBERTO',
                'primer_apellido' => 'RUIZ',
                'segundo_apellido' => 'MENDOZA',
                'correo' => 'caruiz@propietario.test.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3105550001',
                'fecha_nacimiento' => '1970-01-01',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5, // PROPIETARIO
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2000000002,
                'NIT' => 900123456,
                'primer_nombre' => 'PEDRO',
                'segundo_nombre' => 'LUIS',
                'primer_apellido' => 'GOMEZ',
                'segundo_apellido' => 'CADENA',
                'correo' => 'plgomez@propietario.test.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3105550002',
                'fecha_nacimiento' => '1975-05-10',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2000000003,
                'NIT' => 900123456,
                'primer_nombre' => 'LAURA',
                'segundo_nombre' => 'SOFIA',
                'primer_apellido' => 'MARTINEZ',
                'segundo_apellido' => 'LOPEZ',
                'correo' => 'lsmartinez@propietario.test.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3105550003',
                'fecha_nacimiento' => '1985-03-22',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2000000004,
                'NIT' => 900123456,
                'primer_nombre' => 'DIEGO',
                'segundo_nombre' => 'FERNANDO',
                'primer_apellido' => 'ROJAS',
                'segundo_apellido' => 'PEREZ',
                'correo' => 'dfrojas@propietario.test.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3105550004',
                'fecha_nacimiento' => '1980-08-15',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ],
            [
                'doc_usuario' => 2000000005,
                'NIT' => 900123456,
                'primer_nombre' => 'MARIA',
                'segundo_nombre' => 'ELENA',
                'primer_apellido' => 'CASTRO',
                'segundo_apellido' => 'RIOS',
                'correo' => 'mecastro@propietario.test.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3105550005',
                'fecha_nacimiento' => '1978-11-30',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ],
            [
                'doc_usuario' => 2000000006,
                'NIT' => 900123456,
                'primer_nombre' => 'JORGE',
                'segundo_nombre' => 'MARIO',
                'primer_apellido' => 'VARGAS',
                'segundo_apellido' => 'ORTIZ',
                'correo' => 'jmvargas@propietario.test.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3105550006',
                'fecha_nacimiento' => '1982-02-28',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ]
        ]);




        // --- AUXILIARES, JEFES Y COORDINADORES ---
        $empresasTransporte = [
            ['NIT' => 900123456, 'prefix' => '000', 'correo' => 'transportetest.com'],
        ];

        $nombresAux = [
            ['Carlos', 'Eduardo'], ['Maria', 'Fernanda'], ['Juan', 'Pablo'], ['Ana', 'Lucia'], ['Jorge', 'Luis'],
            ['Sonia', 'Patricia'], ['Raul', 'Andres'], ['Isabel', 'Cristina'], ['Pedro', 'Antonio'], ['Marta', 'Cecilia']
        ];
        $apellidosAux = [
            ['Lopez', 'Auxiliar'], ['Garcia', 'Control'], ['Martinez', 'Vigilancia'], ['Rodriguez', 'Apoyo'], ['Perez', 'Operativo'],
            ['Sanchez', 'Logistica'], ['Gomez', 'Terminal'], ['Diaz', 'Rutas'], ['Torres', 'Despacho'], ['Ramirez', 'Empresa']
        ];

        foreach ($empresasTransporte as $empresa) {
            $data = [];
            for ($i = 0; $i < 10; $i++) {
                $doc = ($empresa['prefix'] == '000')
                    ? 4000000000 + ($i + 1)
                    : 4100000000 + ($empresa['prefix'] * 1000) + ($i + 1);

                $data[] = [
                    'doc_usuario' => $doc,
                    'NIT' => $empresa['NIT'],
                    'primer_nombre' => $nombresAux[$i][0],
                    'segundo_nombre' => $nombresAux[$i][1],
                    'primer_apellido' => $apellidosAux[$i][0],
                    'segundo_apellido' => $apellidosAux[$i][1],
                    'correo' => strtolower($nombresAux[$i][0]) . ($i + 1) . "@" . $empresa['correo'],
                    'password' => Hash::make('Auxiliar123*'),
                    'telefono' => '300' . str_pad($i + 100, 7, '0', STR_PAD_LEFT),
                    'fecha_nacimiento' => '1990-05-10',
                    'foto_usuario' => null,
                    'id_tipo_usuario' => 4, // AUXILIAR EMPRESA
                    'id_ciudad' => '730001',
                    'id_estado' => 1
                ];
            }
            DB::table('usuario')->insertOrIgnore($data);
        }

        // --- JEFE DE MANTENIMIENTO (doc_usuario inicia en 9) ---
        $nombresMant = [
            ['Ricardo', 'Antonio'], ['Sandra', 'Milena'], ['Carlos', 'Andres'], ['Marta', 'Lucia'], ['Luis', 'Alberto']
        ];
        $apellidosMant = [
            ['Morales', 'Mantenimiento'], ['Gomez', 'Taller'], ['Ruiz', 'Mecatronica'], ['Pinto', 'Diesel'], ['Vela', 'Equipos']
        ];

        foreach ($empresasTransporte as $empresa) {
            $dataMant = [];
            for ($i = 0; $i < 5; $i++) {
                $doc = ($empresa['prefix'] == '000')
                    ? 9000000000 + ($i + 1)
                    : 9100000000 + ($empresa['prefix'] * 1000) + ($i + 1);

                $dataMant[] = [
                    'doc_usuario' => $doc,
                    'NIT' => $empresa['NIT'],
                    'primer_nombre' => $nombresMant[$i][0],
                    'segundo_nombre' => $nombresMant[$i][1],
                    'primer_apellido' => $apellidosMant[$i][0],
                    'segundo_apellido' => $apellidosMant[$i][1],
                    'correo' => "mantenimiento" . ($i + 1) . "@" . $empresa['correo'],
                    'password' => Hash::make('Mantenimiento123*'),
                    'telefono' => '310' . str_pad(($i + 1) + 50, 7, '0', STR_PAD_LEFT),
                    'fecha_nacimiento' => '1982-08-15',
                    'foto_usuario' => null,
                    'id_tipo_usuario' => 9, // JEFE DE MANTENIMIENTO
                    'id_ciudad' => '730001',
                    'id_estado' => 1
                ];
            }
            DB::table('usuario')->insertOrIgnore($dataMant);
        }

        // --- COORDINADOR (doc_usuario inicia en 7) ---
        $nombresCoord = [
            ['Andres', 'Felipe'], ['Camila', 'Andrea'], ['Diego', 'Fernando'], ['Elena', 'Patricia'], ['Fabio', 'Nelson']
        ];
        $apellidosCoord = [
            ['Beltran', 'Coordinador'], ['Cortes', 'Logistica'], ['Duarte', 'Operaciones'], ['Espitia', 'Rutas'], ['Fajardo', 'Control']
        ];

        foreach ($empresasTransporte as $empresa) {
            $dataCoord = [];
            for ($i = 0; $i < 5; $i++) {
                $doc = ($empresa['prefix'] == '000')
                    ? 7000000000 + ($i + 1)
                    : 7100000000 + ($empresa['prefix'] * 1000) + ($i + 1);

                $dataCoord[] = [
                    'doc_usuario' => $doc,
                    'NIT' => $empresa['NIT'],
                    'primer_nombre' => $nombresCoord[$i][0],
                    'segundo_nombre' => $nombresCoord[$i][1],
                    'primer_apellido' => $apellidosCoord[$i][0],
                    'segundo_apellido' => $apellidosCoord[$i][1],
                    'correo' => "coordinador" . ($i + 1) . "@" . $empresa['correo'],
                    'password' => Hash::make('Coordinador123*'),
                    'telefono' => '315' . str_pad(($i + 1) + 80, 7, '0', STR_PAD_LEFT),
                    'fecha_nacimiento' => '1988-03-20',
                    'foto_usuario' => null,
                    'id_tipo_usuario' => 7, // COORDINADOR BUS
                    'id_ciudad' => '730001',
                    'id_estado' => 1
                ];
            }
            DB::table('usuario')->insertOrIgnore($dataCoord);
        }

        // --- ADMIN RECARGAS (id_tipo_usuario = 10) - EMPRESA DE RECARGA (NIT 800222333) ---
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario'     => 8002223011,
                'NIT'             => 800222333, // SUPERGIROS GANA GANA (EMPRESA DE RECARGA)
                'primer_nombre'   => 'CARLOS',
                'segundo_nombre'  => 'ANDRES',
                'primer_apellido' => 'MENDOZA',
                'segundo_apellido'=> 'RIOS',
                'correo'          => 'cmendoza@supergiros.com',
                'password'        => Hash::make('AdminRecargas123*'),
                'telefono'        => '3001110001',
                'fecha_nacimiento'=> '1982-04-15',
                'foto_usuario'    => null,
                'id_tipo_usuario' => 10, // ADMIN RECARGAS
                'id_ciudad'       => '730001',
                'id_estado'       => 1
            ],
            [
                'doc_usuario'     => 8002223012,
                'NIT'             => 800222333,
                'primer_nombre'   => 'LUCIA',
                'segundo_nombre'  => 'MARCELA',
                'primer_apellido' => 'TORRES',
                'segundo_apellido'=> 'VARGAS',
                'correo'          => 'ltorres@supergiros.com',
                'password'        => Hash::make('AdminRecargas123*'),
                'telefono'        => '3001110002',
                'fecha_nacimiento'=> '1987-09-22',
                'foto_usuario'    => null,
                'id_tipo_usuario' => 10, // ADMIN RECARGAS
                'id_ciudad'       => '730001',
                'id_estado'       => 1
            ],
            [
                'doc_usuario'     => 8002223013,
                'NIT'             => 800222333,
                'primer_nombre'   => 'FELIX',
                'segundo_nombre'  => 'ERNESTO',
                'primer_apellido' => 'GUERRERO',
                'segundo_apellido'=> 'SALCEDO',
                'correo'          => 'fguerrero@supergiros.com',
                'password'        => Hash::make('AdminRecargas123*'),
                'telefono'        => '3001110003',
                'fecha_nacimiento'=> '1979-12-08',
                'foto_usuario'    => null,
                'id_tipo_usuario' => 10, // ADMIN RECARGAS
                'id_ciudad'       => '730001',
                'id_estado'       => 1
            ],
            [
                'doc_usuario'     => 8002223014,
                'NIT'             => 800222333,
                'primer_nombre'   => 'PAOLA',
                'segundo_nombre'  => 'VIVIANA',
                'primer_apellido' => 'CASTILLO',
                'segundo_apellido'=> 'BEDOYA',
                'correo'          => 'pcastillo@supergiros.com',
                'password'        => Hash::make('AdminRecargas123*'),
                'telefono'        => '3001110004',
                'fecha_nacimiento'=> '1991-06-30',
                'foto_usuario'    => null,
                'id_tipo_usuario' => 10, // ADMIN RECARGAS
                'id_ciudad'       => '730001',
                'id_estado'       => 2 // INACTIVO
            ],
        ]);

        // --- GESTOR DE RECARGAS (doc_usuario inicia en 8) ---
        $dataGestores = [];
        $nombresGestor = [
            ['Luis', 'Fernando'], ['Beatriz', 'Elena'], ['Carlos', 'Andres'], ['Diana', 'Marcela'], ['Eduardo', 'Jose'],
            ['Gloria', 'Patricia'], ['Hugo', 'Hernan'], ['Irene', 'Cecilia'], ['Javier', 'Eduardo'], ['Karla', 'Andrea']
        ];
        $apellidosGestor = [
            ['Castro', 'Recaudos'], ['Perez', 'Gana'], ['Gomez', 'Super'], ['Ruiz', 'Puntos'], ['Lopez', 'Giros'],
            ['Vargas', 'Recarga'], ['Mendoza', 'Saldo'], ['Rios', 'Caja'], ['Silva', 'Ventas'], ['Torres', 'Paga']
        ];

        for ($i = 0; $i < 10; $i++) {
            $dataGestores[] = [
                'doc_usuario' => 8000000000 + ($i + 1),
                'NIT' => 800222333, // SUPERGIROS GANA GANA
                'primer_nombre' => $nombresGestor[$i][0],
                'segundo_nombre' => $nombresGestor[$i][1],
                'primer_apellido' => $apellidosGestor[$i][0],
                'segundo_apellido' => $apellidosGestor[$i][1],
                'correo' => "gestor" . ($i + 1) . "@pagatodo.com",
                'password' => Hash::make('Gestor123*'),
                'telefono' => '300' . str_pad($i + 500, 7, '0', STR_PAD_LEFT),
                'fecha_nacimiento' => '1985-04-10',
                'foto_usuario' => null,
                'id_tipo_usuario' => 8, // GESTOR DE RECARGAS
                'id_ciudad' => '730001',
                'id_estado' => 1
            ];
        }
        DB::table('usuario')->insertOrIgnore($dataGestores);

        // --- GESTOR SETP (doc_usuario inicia en 6) ---
        $dataSetp = [];
        $nombresSetp = [
            ['Alberto', 'Enrique'], ['Claudia', 'Lucia'], ['Fernando', 'Jose'], ['Gloria', 'Ines'], ['Mauro', 'Javier']
        ];
        $apellidosSetp = [
            ['Gomez', 'Estrategia'], ['Perez', 'Planeacion'], ['Rodriguez', 'Movilidad'], ['Martinez', 'Gestion'], ['Lopez', 'Control']
        ];

        for ($i = 0; $i < 5; $i++) {
            $dataSetp[] = [
                'doc_usuario' => 6000000000 + ($i + 1),
                'NIT' => 600123456, // SETP IBAGUE S.A.S
                'primer_nombre' => $nombresSetp[$i][0],
                'segundo_nombre' => $nombresSetp[$i][1],
                'primer_apellido' => $apellidosSetp[$i][0],
                'segundo_apellido' => $apellidosSetp[$i][1],
                'correo' => "gestor.setp" . ($i + 1) . "@setp.com",
                'password' => Hash::make('Setp123*'),
                'telefono' => '320' . str_pad($i + 900, 7, '0', STR_PAD_LEFT),
                'fecha_nacimiento' => '1980-01-01',
                'foto_usuario' => null,
                'id_tipo_usuario' => 6, // GESTOR SETP
                'id_ciudad' => '730001',
                'id_estado' => 1
            ];
        }
        DB::table('usuario')->insertOrIgnore($dataSetp);


        // --- ADMIN RECARGAS (nuevo rol 10) ---
        info('Intentando insertar ADMIN RECARGAS en el seeder');
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 8002223010,
                'NIT' => 800222333,
                'primer_nombre' => 'ADMIN',
                'segundo_nombre' => 'RECARGAS',
                'primer_apellido' => 'SUPERGIROS',
                'segundo_apellido' => 'GANA',
                'correo' => 'adminrecargas@pagatodo.com',
                'password' => Hash::make('AdminRecargas123*'),
                'telefono' => '3009999999',
                'fecha_nacimiento' => '1980-01-01',
                'foto_usuario' => null,
                'id_tipo_usuario' => 10, // ADMIN RECARGAS
                'id_ciudad' => '730001',
                'id_estado' => 1
            ]
        ]);

        // --- CONDUCTORES (doc_usuario inicia en 3) ---
        $conductoresData = [];
        $licenciasData = [];

        $nombresRandom = ['Carlos', 'Andrés', 'Luis', 'Javier', 'Miguel', 'José', 'Francisco', 'Manuel', 'Santiago', 'Sebastián', 'Mateo', 'Alejandro', 'Diego', 'Fernando', 'Ricardo'];
        $nombresRandom2 = ['Antonio', 'Felipe', 'Eduardo', 'Alberto', 'María', 'Lucía', 'Elena', 'Beatriz', 'Isabel', 'Cristina', 'Valeria', 'Daniela', 'Sofía', 'Camila', 'Andrea'];
        $apellidosRandom = ['Gómez', 'Rodríguez', 'Pérez', 'Sánchez', 'Martínez', 'Espinosa', 'López', 'Torres', 'Ramírez', 'Moreno', 'Vargas', 'Castro', 'Ortiz', 'Rojas', 'Ruiz'];
        $apellidosRandom2 = ['Mendoza', 'Salazar', 'Cadena', 'Vela', 'Duarte', 'Beltrán', 'Arango', 'Gutiérrez', 'Flórez', 'Quintero', 'Bustamante', 'Osorio', 'Ríos', 'Silva', 'Castaño'];

        for ($i = 0; $i < 10; $i++) {
            $doc = 3000000000 + ($i + 1);
            $nombre1 = $nombresRandom[array_rand($nombresRandom)];
            $nombre2 = $nombresRandom2[array_rand($nombresRandom2)];
            $apellido1 = $apellidosRandom[array_rand($apellidosRandom)];
            $apellido2 = $apellidosRandom2[array_rand($apellidosRandom2)];

            $conductoresData[] = [
                'doc_usuario' => $doc,
                'NIT' => 900123456, // Empresa TEST
                'primer_nombre' => strtoupper($nombre1),
                'segundo_nombre' => strtoupper($nombre2),
                'primer_apellido' => strtoupper($apellido1),
                'segundo_apellido' => strtoupper($apellido2),
                'correo' => strtolower($nombre1 . "." . $apellido1 . $i) . "@transporte.com",
                'password' => Hash::make('Conductor123*'),
                'telefono' => '310'.str_pad($i + 1, 7, '0', STR_PAD_LEFT),
                'fecha_nacimiento' => '198' . rand(0, 9) . '-01-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'foto_usuario' => null,
                'id_tipo_usuario' => 3, // CONDUCTOR
                'id_ciudad' => '730001',
                'id_estado' => 1
            ];

            $licenciasData[] = [
                'nombre' => 'LICENCIA CONDUCCIÓN',
                'archivo' => 'uploads/documentos/licencia_default.png',
                'fecha_expedicion' => '2024-01-01',
                'fecha_vencimiento' => '2027-01-01',
                'id_tipo_documento' => 3, // Licencia
                'doc_usuario' => $doc,
                'NIT' => 900123456,
                'id_estado' => 1, // ACTIVO
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        DB::table('usuario')->insertOrIgnore($conductoresData);
        DB::table('documentos')->insertOrIgnore($licenciasData);
        info('Agregados 10 conductores con nombres reales y licencias vinculadas.');
    }





}

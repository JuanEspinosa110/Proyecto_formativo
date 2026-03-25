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

        // --- Empresa 2: EXPRESO IBAGUÉ S.A. (900111222) ---
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 1100111001,
                'NIT' => 900111222,
                'primer_nombre' => 'JUAN',
                'segundo_nombre' => 'DIEGO',
                'primer_apellido' => 'GOMEZ',
                'segundo_apellido' => 'BARRERO',
                'correo' => 'jdgomez@expresoibague.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3121112233',
                'fecha_nacimiento' => '1982-03-12',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 1100111002,
                'NIT' => 900111222,
                'primer_nombre' => 'MARIA',
                'segundo_nombre' => 'CAMILA',
                'primer_apellido' => 'RESTREPO',
                'segundo_apellido' => 'DIAZ',
                'correo' => 'mcrestrepo@expresoibague.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3132223344',
                'fecha_nacimiento' => '1988-07-25',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 1100111003,
                'NIT' => 900111222,
                'primer_nombre' => 'ANDRES',
                'segundo_nombre' => 'FELIPE',
                'primer_apellido' => 'ORTIZ',
                'segundo_apellido' => 'GOMEZ',
                'correo' => 'afortiz@expresoibague.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3143334455',
                'fecha_nacimiento' => '1992-01-05',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ]
        ]);

        // --- Empresa 3: COTRAUTOL S.A.S. (900333444) ---
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 1100333001,
                'NIT' => 900333444,
                'primer_nombre' => 'LUIS',
                'segundo_nombre' => 'FERNANDO',
                'primer_apellido' => 'HERNANDEZ',
                'segundo_apellido' => 'ROA',
                'correo' => 'lfhernandez@cotrautol.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3151112233',
                'fecha_nacimiento' => '1975-11-30',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 1100333002,
                'NIT' => 900333444,
                'primer_nombre' => 'SANDRA',
                'segundo_nombre' => 'MILENA',
                'primer_apellido' => 'CASTRO',
                'segundo_apellido' => 'BUENDIA',
                'correo' => 'smcastro@cotrautol.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3162223344',
                'fecha_nacimiento' => '1984-04-18',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 1100333003,
                'NIT' => 900333444,
                'primer_nombre' => 'CRISTIAN',
                'segundo_nombre' => 'DAVID',
                'primer_apellido' => 'ROJAS',
                'segundo_apellido' => 'MURCIA',
                'correo' => 'cdrojas@cotrautol.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3173334455',
                'fecha_nacimiento' => '1995-12-22',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ]
        ]);

        // --- Empresa 4: TRANSPORTES LA IBAGUEREÑA S.A.S. (900555666) ---
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 1100555001,
                'NIT' => 900555666,
                'primer_nombre' => 'RICARDO',
                'segundo_nombre' => 'ALFONSO',
                'primer_apellido' => 'PARDO',
                'segundo_apellido' => 'JIMENEZ',
                'correo' => 'rpardo@laibaguereña.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3181112233',
                'fecha_nacimiento' => '1978-09-08',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 1100555002,
                'NIT' => 900555666,
                'primer_nombre' => 'ANGELA',
                'segundo_nombre' => 'MARIA',
                'primer_apellido' => 'SANTOS',
                'segundo_apellido' => 'VERA',
                'correo' => 'asantos@laibaguereña.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3192223344',
                'fecha_nacimiento' => '1986-06-14',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 1100555003,
                'NIT' => 900555666,
                'primer_nombre' => 'JOSE',
                'segundo_nombre' => 'IGNACIO',
                'primer_apellido' => 'VARGAS',
                'segundo_apellido' => 'PEÑA',
                'correo' => 'jvargas@laibaguereña.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3203334455',
                'fecha_nacimiento' => '1993-02-28',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ]
        ]);

        // --- Empresa 5: EXPRESO PURIFICACIÓN S.A. (900777888) ---
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 1100777001,
                'NIT' => 900777888,
                'primer_nombre' => 'GLORIA',
                'segundo_nombre' => 'FANNY',
                'primer_apellido' => 'MARTINEZ',
                'segundo_apellido' => 'PEREZ',
                'correo' => 'gfmartinez@expresopurificacion.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3211112233',
                'fecha_nacimiento' => '1981-12-05',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 1100777002,
                'NIT' => 900777888,
                'primer_nombre' => 'OSCAR',
                'segundo_nombre' => 'IVAN',
                'primer_apellido' => 'GARCIA',
                'segundo_apellido' => 'LOAIZA',
                'correo' => 'oigarcia@expresopurificacion.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3222223344',
                'fecha_nacimiento' => '1989-05-20',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 1100777003,
                'NIT' => 900777888,
                'primer_nombre' => 'NATALIA',
                'segundo_nombre' => 'ANDREA',
                'primer_apellido' => 'PEÑA',
                'segundo_apellido' => 'QUINTERO',
                'correo' => 'napena@expresopurificacion.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3233334455',
                'fecha_nacimiento' => '1994-08-15',
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

        // Empresa 2: EXPRESO IBAGUÉ S.A. (900111222)
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 2100111001,
                'NIT' => 900111222,
                'primer_nombre' => 'ARTURO',
                'segundo_nombre' => 'MANUEL',
                'primer_apellido' => 'PINEDA',
                'segundo_apellido' => 'GARCIA',
                'correo' => 'apineda@propietario.expresoibague.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3115550001',
                'fecha_nacimiento' => '1972-06-15',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100111002,
                'NIT' => 900111222,
                'primer_nombre' => 'BEATRIZ',
                'segundo_nombre' => 'ELENA',
                'primer_apellido' => 'PINZON',
                'segundo_apellido' => 'SOLANO',
                'correo' => 'bpinzon@propietario.expresoibague.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3115550002',
                'fecha_nacimiento' => '1979-09-22',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100111003,
                'NIT' => 900111222,
                'primer_nombre' => 'CAMILO',
                'segundo_nombre' => 'ANDRES',
                'primer_apellido' => 'SESTO',
                'segundo_apellido' => 'BUITRAGO',
                'correo' => 'csesto@propietario.expresoibague.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3115550003',
                'fecha_nacimiento' => '1984-12-05',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100111004,
                'NIT' => 900111222,
                'primer_nombre' => 'DARIO',
                'segundo_nombre' => 'JOSE',
                'primer_apellido' => 'GOMEZ',
                'segundo_apellido' => 'ZAPATA',
                'correo' => 'dgomez@propietario.expresoibague.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3115550004',
                'fecha_nacimiento' => '1976-02-14',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ],
            [
                'doc_usuario' => 2100111005,
                'NIT' => 900111222,
                'primer_nombre' => 'ESPERANZA',
                'segundo_nombre' => 'MARIA',
                'primer_apellido' => 'GOMEZ',
                'segundo_apellido' => 'SILVA',
                'correo' => 'egomez@propietario.expresoibague.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3115550005',
                'fecha_nacimiento' => '1981-05-18',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ],
            [
                'doc_usuario' => 2100111006,
                'NIT' => 900111222,
                'primer_nombre' => 'FABIO',
                'segundo_nombre' => 'ALEXANDER',
                'primer_apellido' => 'ZULETA',
                'segundo_apellido' => 'DIAZ',
                'correo' => 'fzuleta@propietario.expresoibague.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3115550006',
                'fecha_nacimiento' => '1974-08-25',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ]
        ]);

        // Empresa 3: COTRAUTOL S.A.S. (900333444)
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 2100333001,
                'NIT' => 900333444,
                'primer_nombre' => 'GABRIEL',
                'segundo_nombre' => 'JOSE',
                'primer_apellido' => 'GARCIA',
                'segundo_apellido' => 'MARQUEZ',
                'correo' => 'ggarcia@propietario.cotrautol.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3125550001',
                'fecha_nacimiento' => '1971-03-06',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100333002,
                'NIT' => 900333444,
                'primer_nombre' => 'HELENA',
                'segundo_nombre' => 'PATRICIA',
                'primer_apellido' => 'VARGAS',
                'segundo_apellido' => 'OSORIO',
                'correo' => 'hvargas@propietario.cotrautol.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3125550002',
                'fecha_nacimiento' => '1983-05-12',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100333003,
                'NIT' => 900333444,
                'primer_nombre' => 'IVAN',
                'segundo_nombre' => 'RENE',
                'primer_apellido' => 'VILLAZON',
                'segundo_apellido' => 'PALACIO',
                'correo' => 'ivillazon@propietario.cotrautol.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3125550003',
                'fecha_nacimiento' => '1977-10-25',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100333004,
                'NIT' => 900333444,
                'primer_nombre' => 'JORGE',
                'segundo_nombre' => 'ELIECER',
                'primer_apellido' => 'OÑATE',
                'segundo_apellido' => 'RIVERA',
                'correo' => 'joñate@propietario.cotrautol.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3125550004',
                'fecha_nacimiento' => '1979-02-14',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ],
            [
                'doc_usuario' => 2100333005,
                'NIT' => 900333444,
                'primer_nombre' => 'KAROLL',
                'segundo_nombre' => 'VANESSA',
                'primer_apellido' => 'MARQUEZ',
                'segundo_apellido' => 'NIETO',
                'correo' => 'kmarquez@propietario.cotrautol.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3125550005',
                'fecha_nacimiento' => '1987-04-18',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ],
            [
                'doc_usuario' => 2100333006,
                'NIT' => 900333444,
                'primer_nombre' => 'LUIS',
                'segundo_nombre' => 'ANDRES',
                'primer_apellido' => 'ALBERTO',
                'segundo_apellido' => 'POSADA',
                'correo' => 'lalberto@propietario.cotrautol.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3125550006',
                'fecha_nacimiento' => '1981-06-30',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ]
        ]);

        // Empresa 4: TRANSPORTES LA IBAGUEREÑA S.A.S. (900555666)
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 2100555001,
                'NIT' => 900555666,
                'primer_nombre' => 'MONICA',
                'segundo_nombre' => 'ANDREA',
                'primer_apellido' => 'GIRALDO',
                'segundo_apellido' => 'QUINTERO',
                'correo' => 'mgiraldo@propietario.laibaguereña.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3135550001',
                'fecha_nacimiento' => '1982-08-10',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100555002,
                'NIT' => 900555666,
                'primer_nombre' => 'NELSON',
                'segundo_nombre' => 'ENRIQUE',
                'primer_apellido' => 'VELASQUEZ',
                'segundo_apellido' => 'SANTOS',
                'correo' => 'nvelasquez@propietario.laibaguereña.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3135550002',
                'fecha_nacimiento' => '1976-03-22',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100555003,
                'NIT' => 900555666,
                'primer_nombre' => 'ORLANDO',
                'segundo_nombre' => 'RAFAEL',
                'primer_apellido' => 'LIÑAN',
                'segundo_apellido' => 'HERRERA',
                'correo' => 'oliñan@propietario.laibaguereña.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3135550003',
                'fecha_nacimiento' => '1980-05-15',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100555004,
                'NIT' => 900555666,
                'primer_nombre' => 'PAOLA',
                'segundo_nombre' => 'ANDREA',
                'primer_apellido' => 'JARA',
                'segundo_apellido' => 'VARGAS',
                'correo' => 'pjara@propietario.laibaguereña.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3135550004',
                'fecha_nacimiento' => '1983-09-03',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ],
            [
                'doc_usuario' => 2100555005,
                'NIT' => 900555666,
                'primer_nombre' => 'QUIQUE',
                'segundo_nombre' => 'ALBERTO',
                'primer_apellido' => 'SANTANDER',
                'segundo_apellido' => 'DIAZ',
                'correo' => 'qsantander@propietario.laibaguereña.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3135550005',
                'fecha_nacimiento' => '1974-11-12',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ],
            [
                'doc_usuario' => 2100555006,
                'NIT' => 900555666,
                'primer_nombre' => 'ROBERTO',
                'segundo_nombre' => 'CARLOS',
                'primer_apellido' => 'OSPINO',
                'segundo_apellido' => 'GOMEZ',
                'correo' => 'rcarlos@propietario.laibaguereña.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3135550006',
                'fecha_nacimiento' => '1971-06-30',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ]
        ]);

        // Empresa 5: EXPRESO PURIFICACIÓN S.A. (900777888)
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 2100777001,
                'NIT' => 900777888,
                'primer_nombre' => 'SILVESTRE',
                'segundo_nombre' => 'JOSE',
                'primer_apellido' => 'DANGOND',
                'segundo_apellido' => 'CORRALES',
                'correo' => 'sdangond@propietario.expresopurificacion.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3205550001',
                'fecha_nacimiento' => '1980-05-12',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100777002,
                'NIT' => 900777888,
                'primer_nombre' => 'IVAN',
                'segundo_nombre' => 'ANDRES',
                'primer_apellido' => 'CALDERON',
                'segundo_apellido' => 'CASTILLA',
                'correo' => 'icalderon@propietario.expresopurificacion.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3205550002',
                'fecha_nacimiento' => '1978-02-14',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100777003,
                'NIT' => 900777888,
                'primer_nombre' => 'ULISES',
                'segundo_nombre' => 'ANDRES',
                'primer_apellido' => 'BUENO',
                'segundo_apellido' => 'ROJAS',
                'correo' => 'ubueno@propietario.expresopurificacion.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3205550003',
                'fecha_nacimiento' => '1985-11-20',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2100777004,
                'NIT' => 900777888,
                'primer_nombre' => 'VICTOR',
                'segundo_nombre' => 'MANUEL',
                'primer_apellido' => 'RIVERA',
                'segundo_apellido' => 'ROSARIO',
                'correo' => 'vrivera@propietario.expresopurificacion.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3205550004',
                'fecha_nacimiento' => '1973-04-10',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ],
            [
                'doc_usuario' => 2100777005,
                'NIT' => 900777888,
                'primer_nombre' => 'WILSON',
                'segundo_nombre' => 'MANUEL',
                'primer_apellido' => 'MANYOMA',
                'segundo_apellido' => 'GIL',
                'correo' => 'wmanyoma@propietario.expresopurificacion.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3205550005',
                'fecha_nacimiento' => '1970-08-25',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ],
            [
                'doc_usuario' => 2100777006,
                'NIT' => 900777888,
                'primer_nombre' => 'YURI',
                'segundo_nombre' => 'ELIECER',
                'primer_apellido' => 'BUENAVENTURA',
                'segundo_apellido' => 'MIA',
                'correo' => 'ybuenaventura@propietario.expresopurificacion.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3205550006',
                'fecha_nacimiento' => '1974-05-30',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5,
                'id_ciudad' => '730001',
                'id_estado' => 2 // INACTIVO
            ]
        ]);


        // --- CONDUCTORES ---

        // Empresa 1: TRANSPORTE TEST SAS (900123456)
        DB::table('usuario')->insertOrIgnore([
            ['doc_usuario' => 3000000001, 'NIT' => 900123456, 'primer_nombre' => 'RICARDO', 'primer_apellido' => 'MORALES', 'correo' => 'rmorales@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 
            ['doc_usuario' => 3000000002, 'NIT' => 900123456, 'primer_nombre' => 'SANDRA', 'primer_apellido' => 'MILENA', 'correo' => 'smilena@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 
            ['doc_usuario' => 3000000003, 'NIT' => 900123456, 'primer_nombre' => 'CARLOS', 'primer_apellido' => 'EDUARDO', 'correo' => 'ceduardo@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 
            ['doc_usuario' => 3000000004, 'NIT' => 900123456, 'primer_nombre' => 'MARTA', 'primer_apellido' => 'CECILIA', 'correo' => 'mcecilia@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 
            ['doc_usuario' => 3000000005, 'NIT' => 900123456, 'primer_nombre' => 'LUIS', 'primer_apellido' => 'ALBERTO', 'correo' => 'lalberto@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3000000006, 'NIT' => 900123456, 'primer_nombre' => 'ANDREA', 'primer_apellido' => 'PAOLA', 'correo' => 'apaola@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3000000007, 'NIT' => 900123456, 'primer_nombre' => 'JORGE', 'primer_apellido' => 'ANDRES', 'correo' => 'jandres@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3000000008, 'NIT' => 900123456, 'primer_nombre' => 'GLORIA', 'primer_apellido' => 'ISABEL', 'correo' => 'gisabel@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3000000009, 'NIT' => 900123456, 'primer_nombre' => 'DIEGO', 'primer_apellido' => 'ARMANDO', 'correo' => 'darmando@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3000000010, 'NIT' => 900123456, 'primer_nombre' => 'JULIA', 'primer_apellido' => 'ESTHER', 'correo' => 'jesther@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3000000011, 'NIT' => 900123456, 'primer_nombre' => 'OSCAR', 'primer_apellido' => 'JAVIER', 'correo' => 'ojavier@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3000000012, 'NIT' => 900123456, 'primer_nombre' => 'SONIA', 'primer_apellido' => 'PATRICIA', 'correo' => 'spatricia@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3000000013, 'NIT' => 900123456, 'primer_nombre' => 'MARIO', 'primer_apellido' => 'AUGUSTO', 'correo' => 'maugusto@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3000000014, 'NIT' => 900123456, 'primer_nombre' => 'BEATRIZ', 'primer_apellido' => 'ADRIANA', 'correo' => 'badriana@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3000000015, 'NIT' => 900123456, 'primer_nombre' => 'HECTOR', 'primer_apellido' => 'FABIO', 'correo' => 'hfabio@conductor.test.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
        ]);

        // Empresa 2: EXPRESO IBAGUÉ S.A. (900111222)
        DB::table('usuario')->insertOrIgnore([
            ['doc_usuario' => 3100111001, 'NIT' => 900111222, 'primer_nombre' => 'ADRIANA', 'primer_apellido' => 'GARCIA', 'correo' => 'agarcia@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 5
            ['doc_usuario' => 3100111002, 'NIT' => 900111222, 'primer_nombre' => 'FELIPE', 'primer_apellido' => 'PINZON', 'correo' => 'fpinzon@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 6
            ['doc_usuario' => 3100111003, 'NIT' => 900111222, 'primer_nombre' => 'LUISA', 'primer_apellido' => 'FERNANDA', 'correo' => 'lfernanda@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 7
            ['doc_usuario' => 3100111004, 'NIT' => 900111222, 'primer_nombre' => 'ESTEBAN', 'primer_apellido' => 'DORANTE', 'correo' => 'edorante@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 8
            ['doc_usuario' => 3100111005, 'NIT' => 900111222, 'primer_nombre' => 'CAROLINA', 'primer_apellido' => 'HERRERA', 'correo' => 'cherrera@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100111006, 'NIT' => 900111222, 'primer_nombre' => 'GERMAN', 'primer_apellido' => 'CASTRO', 'correo' => 'gcastro@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100111007, 'NIT' => 900111222, 'primer_nombre' => 'MONICA', 'primer_apellido' => 'RIVEROS', 'correo' => 'mriveros@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100111008, 'NIT' => 900111222, 'primer_nombre' => 'JAVIER', 'primer_apellido' => 'SOLANO', 'correo' => 'jsolano@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100111009, 'NIT' => 900111222, 'primer_nombre' => 'PATRICIA', 'primer_apellido' => 'CORREA', 'correo' => 'pcorrea@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100111010, 'NIT' => 900111222, 'primer_nombre' => 'RAFAEL', 'primer_apellido' => 'URIBE', 'correo' => 'ruribe@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100111011, 'NIT' => 900111222, 'primer_nombre' => 'ELENA', 'primer_apellido' => 'REYES', 'correo' => 'ereyes@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100111012, 'NIT' => 900111222, 'primer_nombre' => 'SANTIAGO', 'primer_apellido' => 'ALVAREZ', 'correo' => 'salvarez@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100111013, 'NIT' => 900111222, 'primer_nombre' => 'LOLA', 'primer_apellido' => 'MORA', 'correo' => 'lmora@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100111014, 'NIT' => 900111222, 'primer_nombre' => 'WILSON', 'primer_apellido' => 'GOMEZ', 'correo' => 'wgomez@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100111015, 'NIT' => 900111222, 'primer_nombre' => 'OLGA', 'primer_apellido' => 'SANTOS', 'correo' => 'osantos@conductor.expresoibague.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
        ]);

        // Empresa 3: COTRAUTOL S.A.S. (900333444)
        DB::table('usuario')->insertOrIgnore([
            ['doc_usuario' => 3100333001, 'NIT' => 900333444, 'primer_nombre' => 'GUSTAVO', 'primer_apellido' => 'ADOLFO', 'correo' => 'gadolfo@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 9
            ['doc_usuario' => 3100333002, 'NIT' => 900333444, 'primer_nombre' => 'YOLANDA', 'primer_apellido' => 'RUIZ', 'correo' => 'yruiz@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 10
            ['doc_usuario' => 3100333003, 'NIT' => 900333444, 'primer_nombre' => 'RAMIRO', 'primer_apellido' => 'CALDERON', 'correo' => 'rcalderon@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 11
            ['doc_usuario' => 3100333004, 'NIT' => 900333444, 'primer_nombre' => 'CECILIA', 'primer_apellido' => 'RODRIGUEZ', 'correo' => 'crodriguez@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 12
            ['doc_usuario' => 3100333005, 'NIT' => 900333444, 'primer_nombre' => 'BENJAMIN', 'primer_apellido' => 'FRANKLIN', 'correo' => 'bfranklin@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100333006, 'NIT' => 900333444, 'primer_nombre' => 'IRENE', 'primer_apellido' => 'VALLEJO', 'correo' => 'ivallejo@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100333007, 'NIT' => 900333444, 'primer_nombre' => 'ORLANDO', 'primer_apellido' => 'DUQUE', 'correo' => 'oduque@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100333008, 'NIT' => 900333444, 'primer_nombre' => 'SILVIA', 'primer_apellido' => 'GIRALDO', 'correo' => 'sgiraldo@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100333009, 'NIT' => 900333444, 'primer_nombre' => 'RAUL', 'primer_apellido' => 'MOLANO', 'correo' => 'rmolano@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100333010, 'NIT' => 900333444, 'primer_nombre' => 'ISABEL', 'primer_apellido' => 'PANTOJA', 'correo' => 'ipantoja@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100333011, 'NIT' => 900333444, 'primer_nombre' => 'EMILIO', 'primer_apellido' => 'ESTEFAN', 'correo' => 'eestefan@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100333012, 'NIT' => 900333444, 'primer_nombre' => 'REBECA', 'primer_apellido' => 'LINARES', 'correo' => 'rlinares@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100333013, 'NIT' => 900333444, 'primer_nombre' => 'PABLO', 'primer_apellido' => 'EMILIO', 'correo' => 'pemilio@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100333014, 'NIT' => 900333444, 'primer_nombre' => 'VIVIANA', 'primer_apellido' => 'MARTINEZ', 'correo' => 'vmartinez@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100333015, 'NIT' => 900333444, 'primer_nombre' => 'ESTHER', 'primer_apellido' => 'CITA', 'correo' => 'ecita@conductor.cotrautol.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
        ]);

        // Empresa 4: TRANSPORTES LA IBAGUEREÑA S.A.S. (900555666)
        DB::table('usuario')->insertOrIgnore([
            ['doc_usuario' => 3100555001, 'NIT' => 900555666, 'primer_nombre' => 'ALVARO', 'primer_apellido' => 'URIBE', 'correo' => 'auribe@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 13
            ['doc_usuario' => 3100555002, 'NIT' => 900555666, 'primer_nombre' => 'JUAN', 'primer_apellido' => 'MANUEL', 'correo' => 'jmanuel@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 14
            ['doc_usuario' => 3100555003, 'NIT' => 900555666, 'primer_nombre' => 'IVAN', 'primer_apellido' => 'DUQUE', 'correo' => 'iduque@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 15
            ['doc_usuario' => 3100555004, 'NIT' => 900555666, 'primer_nombre' => 'MIGUEL', 'primer_apellido' => 'VARGAS', 'correo' => 'mvargas@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555005, 'NIT' => 900555666, 'primer_nombre' => 'LEONOR', 'primer_apellido' => 'SANTANA', 'correo' => 'lsantana@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555006, 'NIT' => 900555666, 'primer_nombre' => 'PEDRO', 'primer_apellido' => 'PARAMO', 'correo' => 'pparamo@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555007, 'NIT' => 900555666, 'primer_nombre' => 'INES', 'primer_apellido' => 'DUARTE', 'correo' => 'iduarte@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555008, 'NIT' => 900555666, 'primer_nombre' => 'TITO', 'primer_apellido' => 'PUENTE', 'correo' => 'tpuente@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555009, 'NIT' => 900555666, 'primer_nombre' => 'CELIA', 'primer_apellido' => 'CRUZ', 'correo' => 'ccruz@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555010, 'NIT' => 900555666, 'primer_nombre' => 'RUBEN', 'primer_apellido' => 'BLADES', 'correo' => 'rblades@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555011, 'NIT' => 900555666, 'primer_nombre' => 'JUAN', 'primer_apellido' => 'GABRIEL', 'correo' => 'jgabriel@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555012, 'NIT' => 900555666, 'primer_nombre' => 'ROBERTO', 'primer_apellido' => 'CARLOS', 'correo' => 'rcarlos@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555013, 'NIT' => 900555666, 'primer_nombre' => 'ANA', 'primer_apellido' => 'GABRIEL', 'correo' => 'agabriel@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555014, 'NIT' => 900555666, 'primer_nombre' => 'MIGUEL', 'primer_apellido' => 'BOSE', 'correo' => 'mbose@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100555015, 'NIT' => 900555666, 'primer_nombre' => 'SHAKIRA', 'primer_apellido' => 'MEBARAK', 'correo' => 'shakira@conductor.laibaguereña.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
        ]);

        // Empresa 5: EXPRESO PURIFICACIÓN S.A. (900777888)
        DB::table('usuario')->insertOrIgnore([
            ['doc_usuario' => 3100777001, 'NIT' => 900777888, 'primer_nombre' => 'CARLOS', 'primer_apellido' => 'VIVES', 'correo' => 'cvives@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 16
            ['doc_usuario' => 3100777002, 'NIT' => 900777888, 'primer_nombre' => 'JUANES', 'primer_apellido' => 'ARISTIZABAL', 'correo' => 'juanes@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 17
            ['doc_usuario' => 3100777003, 'NIT' => 900777888, 'primer_nombre' => 'JBALVIN', 'primer_apellido' => 'OSORIO', 'correo' => 'jbalvin@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 2, 'password' => Hash::make('Conductor123*')], // INACTIVO 18
            ['doc_usuario' => 3100777004, 'NIT' => 900777888, 'primer_nombre' => 'MALUMA', 'primer_apellido' => 'LONDOÑO', 'correo' => 'maluma@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777005, 'NIT' => 900777888, 'primer_nombre' => 'GREEICY', 'primer_apellido' => 'RENDON', 'correo' => 'greeicy@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777006, 'NIT' => 900777888, 'primer_nombre' => 'MIKE', 'primer_apellido' => 'BAHIA', 'correo' => 'mike@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777007, 'NIT' => 900777888, 'primer_nombre' => 'KAROLG', 'primer_apellido' => 'GIRALDO', 'correo' => 'karolg@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777008, 'NIT' => 900777888, 'primer_nombre' => 'FEID', 'primer_apellido' => 'VILLADA', 'correo' => 'feid@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777009, 'NIT' => 900777888, 'primer_nombre' => 'BLESSD', 'primer_apellido' => 'CASTRILLON', 'correo' => 'blessd@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777010, 'NIT' => 900777888, 'primer_nombre' => 'RYAN', 'primer_apellido' => 'CASTRO', 'correo' => 'ryan@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777011, 'NIT' => 900777888, 'primer_nombre' => 'ANDY', 'primer_apellido' => 'RIVERA', 'correo' => 'andy@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777012, 'NIT' => 900777888, 'primer_nombre' => 'JHONNY', 'primer_apellido' => 'RIVERA', 'correo' => 'jhonny@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777013, 'NIT' => 900777888, 'primer_nombre' => 'ARELYS', 'primer_apellido' => 'HENAO', 'correo' => 'arelys@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777014, 'NIT' => 900777888, 'primer_nombre' => 'FRANCY', 'primer_apellido' => 'DUARTE', 'correo' => 'francy@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
            ['doc_usuario' => 3100777015, 'NIT' => 900777888, 'primer_nombre' => 'PAOLA', 'primer_apellido' => 'JARA', 'correo' => 'pjara@conductor.expresopurificacion.com', 'id_tipo_usuario' => 3, 'id_ciudad' => '730001', 'id_estado' => 1, 'password' => Hash::make('Conductor123*')],
        ]);



        // --- AUXILIARES (doc_usuario inicia en 4) ---
        $empresasTransporte = [
            ['NIT' => 900123456, 'prefix' => '000', 'correo' => 'transportetest.com'],
            ['NIT' => 900111222, 'prefix' => '111', 'correo' => 'expresoibague.com'],
            ['NIT' => 900333444, 'prefix' => '333', 'correo' => 'cotrautol.com'],
            ['NIT' => 900555666, 'prefix' => '555', 'correo' => 'laibaguereña.com'],
            ['NIT' => 900777888, 'prefix' => '777', 'correo' => 'expresopurificacion.com'],
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

    }





}
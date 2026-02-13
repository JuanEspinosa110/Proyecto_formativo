<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarjeta;

class TarjetaController extends Controller
{
    public function index()
    {
        /**
         * ============================
         * 1. KPIs
         * ============================
         */

        $totalTarjetas = Tarjeta::count();

        $tarjetasActivas = Tarjeta::where('id_estado', '1')->count();

        $tarjetasBloqueadas = Tarjeta::where('id_estado', '2')->count();

        $tarjetasSinSaldo = Tarjeta::where('saldo', '<=', 0)->count();

        /**
         * ============================
         * 2. LISTADO DE TARJETAS
         * ============================
         */

        $tarjetas = Tarjeta::orderBy('id_tarjeta', 'desc')->get();

        /**
         * ============================
         * 3. ENVIAR A LA VISTA
         * ============================
         */

        return view('admin.tarjetas.index', compact(
            'totalTarjetas',
            'tarjetasActivas',
            'tarjetasBloqueadas',
            'tarjetasSinSaldo',
            'tarjetas'
        ));
    }



        public function show($id)
            {
                $tarjeta = Tarjeta::findOrFail($id);

                $usuario = null; 
                $movimientos = []; 

                return view('admin.tarjetas.show', compact(
                    'tarjeta',
                    'usuario',
                    'movimientos'
                ));
            }



         public function update(Request $request, Tarjeta $tarjeta)
        {
            $request->validate([
                'codigo_tarjeta' => 'required|string|max:50',
                'saldo' => 'required|numeric|min:0',
                'id_estado' => 'required|integer'
            ]);

            $tarjeta->update([
                'codigo_tarjeta' => $request->codigo_tarjeta,
                'saldo' => $request->saldo,
                'id_estado' => $request->id_estado,
            ]);

            return redirect()
                ->route('admin.tarjetas.show', $tarjeta->id_tarjeta)
                ->with('success', 'Tarjeta actualizada correctamente');
}



}

       

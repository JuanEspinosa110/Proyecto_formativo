<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Muestra el dashboard de la empresa.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return view('empresa.dashboard');
    }
}

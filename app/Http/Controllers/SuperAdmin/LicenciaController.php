<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LicenciaController extends Controller
{
    public function index(){
        return view('superadmin.licencias.index');
    }

    public function create(){
        return view('superadmin.licencias.crear_licencia');
    }

    public function edit($id){
        return view('superadmin.licencias.editar_licencia');
    }

    public function configurarPlan(){
        return view('superadmin.licencias.configurar_plan');
    }
}

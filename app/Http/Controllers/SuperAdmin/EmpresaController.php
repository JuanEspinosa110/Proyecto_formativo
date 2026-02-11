<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa; 

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::paginate(10);
        return view('superadmin.empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('superadmin.empresas.create');
    }

    public function store(Request $request)
    {
        Empresa::create($request->all());
        return redirect()->route('superadmin.empresas.index');
    }

    public function show($id)
    {
        $empresa = Empresa::findOrFail($id);
        return view('superadmin.empresas.show', compact('empresa'));
    }

    public function edit($id)
    {
        $empresa = Empresa::findOrFail($id);
        return view('superadmin.empresas.edit', compact('empresa'));
    }

    public function update(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->update($request->all());

        return redirect()->route('superadmin.empresas.index');
    }

    public function destroy($id)
    {
        Empresa::destroy($id);
        return back();
    }

    public function toggleEstado($id)
    {
        $empresa = Empresa::findOrFail($id);

        $empresa->estado =
            $empresa->estado === 'activa'
                ? 'inactiva'
                : 'activa';

        $empresa->save();

        return back();
    }

    public function auxiliares($id)
    {
        return view('superadmin.empresas.auxiliares');
    }

    public function buses($id)
    {
        return view('superadmin.empresas.buses');
    }

    public function documentos($id)
    {
        return view('superadmin.empresas.documentos');
    }

    public function uploadDocumento(Request $request, $id)
    {
        return back();
    }

    
}

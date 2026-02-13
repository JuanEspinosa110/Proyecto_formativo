<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index(){
        return view('.documentos.index');
    }
}

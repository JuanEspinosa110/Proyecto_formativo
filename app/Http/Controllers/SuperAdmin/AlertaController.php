<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index(){
        return view('admin.alertas.index');
    }
}

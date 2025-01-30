<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ControlPanelController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Bienvenido al panel de control']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Filiere;

class FiliereController extends Controller
{
    public function index()
    {
        $filieres = Filiere::orderBy('order')->orderBy('id')->get();

        return view('public.filieres', compact('filieres'));
    }
}

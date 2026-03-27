<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\DonacionMail;
use Mail;

class DonacionPublicaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'gmail' => 'required|email',
            'telefono' => 'required',
            'ubicacion' => 'required',
            'tipo_donacion' => 'required',
            'mensaje' => 'required'
        ]);

        Mail::to('saladelecturaceyolokalli@gmail.com')->send(new DonacionMail($request));

        return back()->with('success', '¡Gracias! Tu información ha sido enviada.');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PrestamoMail;
use App\Models\Libro;

class PrestamopController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->get('busqueda');
        $libros = Libro::when($busqueda, function ($query, $busqueda) {
            return $query->where('nombre', 'like', "%$busqueda%")
                         ->orWhere('autor', 'like', "%$busqueda%");
        })->get();

        return view('prestamo', compact('libros', 'busqueda'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'telefono' => 'required|string|max:20',
            'libro' => 'required|string|max:255',
        ]);

        Mail::to('saladelecturaceyolokalli@gmail.com')->send(new PrestamoMail($validated));

        // (Opcional) enviar confirmación al usuario
        // Mail::to($validated['email'])->send(new PrestamoMail($validated));

        return back()->with('success', '¡Tu solicitud de préstamo fue enviada correctamente!');
    }
}

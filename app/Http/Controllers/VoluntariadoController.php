<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\VoluntariadoMail;
use App\Models\Voluntariado;
use App\Models\Donacion;
use App\Models\Libro;
use App\Models\Prestamo;

class VoluntariadoController extends Controller
{
    public function index()
    {
        $voluntariado = Voluntariado::all();
        $donaciones = Donacion::all();
        $totalLibros = Libro::count();
        $prestamosActivos = Prestamo::where('estatus', 'prestado')->count();
        $totalDonaciones = $donaciones->count();

        return view('voluntariado', compact(
            'voluntariado',
            'donaciones',
            'totalLibros',
            'prestamosActivos',
            'totalDonaciones'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'gmail' => 'required|email|max:255',
            'telefono' => 'required|string|max:15',
            'tipo_voluntariado' => 'required|string|max:50',
            'mensaje' => 'required|string|max:255',
        ]);

        Voluntariado::create($request->all());

        return redirect()->route('voluntariado.index')
            ->with('success', '✅ Voluntariado registrado correctamente.');
    }

    public function edit($id)
    {
        $voluntariado = Voluntariado::findOrFail($id);
        return view('voluntariado_edit', compact('voluntariado'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'gmail' => 'required|email|max:255',
            'telefono' => 'required|string|max:15',
            'tipo_voluntariado' => 'required|string|max:50',
            'mensaje' => 'required|string|max:255',
        ]);

        $voluntariado = Voluntariado::findOrFail($id);
        $voluntariado->update($request->all());

        return redirect()->route('voluntariado.index')
            ->with('success', '✏️ Voluntariado actualizado correctamente.');
    }

    public function destroy($id)
    {
        Voluntariado::findOrFail($id)->delete();

        return redirect()->route('voluntariado.index')
            ->with('success', '🗑️ Voluntariado eliminado correctamente.');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'telefono' => 'required|string|max:20',
            'tipo_voluntariado' => 'required|string|max:255',
            'mensaje' => 'required|string',
        ]);

        Mail::to('saladelecturaceyolokalli@gmail.com')
            ->send(new VoluntariadoMail($validated));

        return back()->with('success', '¡Tu información fue enviada correctamente!');
    }
}
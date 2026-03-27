<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donacion;
use App\Models\Libro;
use App\Models\Prestamo;

class DonacionController extends Controller
{
    public function index()
    {
        $donaciones = Donacion::all();
        $totalLibros = Libro::count();
        $prestamosActivos = Prestamo::where('estatus', 'prestado')->count();
        $totalDonaciones = $donaciones->count();
        
        return view('donadores', compact('donaciones', 'totalLibros', 'prestamosActivos', 'totalDonaciones'));
    }

    public function getDonaciones()
    {
        $donaciones = Donacion::all();
        return response()->json($donaciones);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'gmail' => 'required|email|max:255',
            'telefono' => 'required|string|max:15',
            'tipo_donacion' => 'required|string|max:50',
        ]);

        Donacion::create([
            'nombre' => $request->nombre,
            'gmail' => $request->gmail,
            'telefono' => $request->telefono,
            'tipo_donacion' => $request->tipo_donacion,
        ]);

        return redirect()->route('donadores')->with('success', '✅ Donación registrada correctamente.');
    }

    public function edit($id)
    {
        $donacion = Donacion::findOrFail($id);
        return view('donaciones_edit', compact('donacion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'gmail' => 'required|email|max:255',
            'telefono' => 'required|string|max:15',
            'tipo_donacion' => 'required|string|max:50',
        ]);

        $donacion = Donacion::findOrFail($id);
        $donacion->update([
            'nombre' => $request->nombre,
            'gmail' => $request->gmail,
            'telefono' => $request->telefono,
            'tipo_donacion' => $request->tipo_donacion,
        ]);

        return redirect()->route('donadores')->with('success', '✏️ Donación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $donacion = Donacion::findOrFail($id);
        $donacion->delete();

        return redirect()->route('donadores')->with('success', '🗑️ Donación eliminada correctamente.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Libro;
use App\Models\Donacion;

class PrestamoController extends Controller
{
    public function index()
    {
        $prestamoss = Prestamo::where('estatus', 'prestado')->get();

        $libros = Libro::all();
        
        $totalLibros = Libro::count();
        $prestamosActivos = Prestamo::where('estatus', 'prestado')->count();
        $todosPrestamos = Prestamo::orderBy('created_at', 'desc')->get();

        try {
            $totalDonaciones = Donacion::count();
        } catch (\Exception $e) {
            $totalDonaciones = 0;
        }
        
        return view('prestamosl', compact('prestamoss'  , 'todosPrestamos', 'totalLibros', 'prestamosActivos', 'totalDonaciones', 'libros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_libro' => 'required|string|max:255',
            'a_quien' => 'required|string|max:255',
            'telefono' => 'required|string|max:15',
            'id_libro' => 'required|string|max:50',
            'fecha_prestamo' => 'required|date',
            'fecha_devolucion' => 'required|date|after_or_equal:fecha_prestamo',
        ], [
            'fecha_devolucion.after_or_equal' => '⚠️ La fecha de devolución debe ser igual o posterior a la fecha de préstamo.',
        ]);

        if (strtotime($request->fecha_devolucion) < strtotime($request->fecha_prestamo)) {
            return redirect()->back()
                ->withErrors(['fecha_devolucion' => '⚠️ La fecha de devolución no puede ser anterior a la fecha de préstamo.'])
                ->withInput();
        }

        Prestamo::create([
            'nombre_libro' => $request->nombre_libro,
            'a_quien' => $request->a_quien,
            'telefono' => $request->telefono,
            'id_libro' => $request->id_libro,
            'fecha_prestamo' => $request->fecha_prestamo,
            'fecha_devolucion' => $request->fecha_devolucion,
            'estatus' => 'prestado'
        ]);

        return redirect()->route('prestamosl')
                         ->with('success', '✅ Préstamo registrado exitosamente.');
    }

    public function marcarDevuelto($id)
    {
        $prestamo = Prestamo::findOrFail($id);
        $prestamo->update(['estatus' => 'devuelto']);


        return redirect()->route('prestamosl')
                         ->with('success', '✅ Préstamo marcado como devuelto.');
    }

    public function edit($id)
    {
        $prestamo = Prestamo::findOrFail($id);
        return view('prestamosl_edit', compact('prestamo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_libro' => 'required|string|max:255',
            'a_quien' => 'required|string|max:255',
            'telefono' => 'required|string|max:15',
            'id_libro' => 'required|string|max:50',
            'fecha_prestamo' => 'required|date',
            'fecha_devolucion' => 'required|date|after_or_equal:fecha_prestamo',
        ], [
            'fecha_devolucion.after_or_equal' => '⚠️ La fecha de devolución debe ser igual o posterior a la fecha de préstamo.',
        ]);

        if (strtotime($request->fecha_devolucion) < strtotime($request->fecha_prestamo)) {
            return redirect()->back()
                ->withErrors(['fecha_devolucion' => '⚠️ La fecha de devolución no puede ser anterior a la fecha de préstamo.'])
                ->withInput();
        }

        $prestamo = Prestamo::findOrFail($id);
        $prestamo->update($request->all());

        return redirect()->route('prestamosl')
                         ->with('success', '✏️ Préstamo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $prestamo = Prestamo::findOrFail($id);
        $prestamo->delete();

        return redirect()->route('prestamosl')
                         ->with('success', '🗑️ Préstamo eliminado correctamente.');
    }
}
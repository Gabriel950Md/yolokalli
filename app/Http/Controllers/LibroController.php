<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Donacion;
use Illuminate\Http\Request;

class LibroController extends Controller
{
    public function index()
    {
        $libros = Libro::all();

        $totalLibros = Libro::count();

       $prestamosActivos = Prestamo::where('estatus', 'prestado')->count();

        try {
            $totalDonaciones = Donacion::count();
        } catch (\Exception $e) {
            $totalDonaciones = 0;
        }

        return view('librosp', compact('libros', 'totalLibros', 'prestamosActivos', 'totalDonaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'estatus' => 'required|string',
            'idLibro' => 'required|string|unique:libros,idLibro',
        ]);
   
        Libro::create([
            'nombre' => $request->nombre,
            'autor' => $request->autor,
            'estatus' => $request->estatus,
            'idLibro' => $request->idLibro,
        ]);
    
        session()->flash('success', '¡Libro agregado con éxito!');
        return redirect()->route('librosp');
    }
    
    public function edit(Libro $libro)
    {
        return view('libros.edit', compact('libro'));
    }

    public function update(Request $request, Libro $libro)
    {
        $request->validate([
            'nombre' => 'required',
            'autor' => 'required',
            'estatus' => 'required',
            'idLibro' => 'required|unique:libros,idLibro,' . $libro->id
        ]);

        $libro->update($request->all());
        return redirect()->route('librosp')->with('success', 'Libro actualizado correctamente');
    }

    public function destroy(Libro $libro)
    {
        $libro->delete();
        return redirect()->route('librosp')->with('success', 'Libro eliminado correctamente');
    }

public function prestamo(Request $request)
{
    $busqueda = $request->input('busqueda');

    $libros = Libro::when($busqueda, function ($query, $busqueda) {
        return $query->where('nombre', 'like', "%{$busqueda}%")
                     ->orWhere('autor', 'like', "%{$busqueda}%");
    })->get();

    foreach ($libros as $libro) {

        $prestado = Prestamo::where('id_libro', $libro->idLibro)
            ->where('estatus', 'prestado')
            ->exists();

        $libro->estatus = $prestado ? 'Prestado' : 'Disponible';
    }

    return view('prestamo', compact('libros', 'busqueda'));
}
}
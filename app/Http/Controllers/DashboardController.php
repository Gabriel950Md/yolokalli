<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Libro;
use App\Models\Donacion;
use App\Models\Prestamo;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLibros = Libro::count();
        $totalDonaciones = Donacion::count();

        $prestamosActivos = Prestamo::where(function($query) {
            $query->whereNull('fecha_devolucion')
                  ->orWhere('fecha_devolucion', '>', now());
        })->count();

        $actividadReciente = $this->getActividadReciente();

        $librosMasPrestados = DB::table('prestamos')
            ->select('nombre_libro', DB::raw('COUNT(*) as total_prestamos'))
            ->groupBy('nombre_libro')
            ->orderByDesc('total_prestamos')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalLibros',
            'totalDonaciones', 
            'prestamosActivos',
            'actividadReciente',
            'librosMasPrestados'
        ));
    }

    private function getActividadReciente()
    {
        $actividad = [];
        
        $ultimosLibros = Libro::latest()->take(2)->get();
        foreach ($ultimosLibros as $libro) {
            $actividad[] = [
                'icono' => 'fas fa-book',
                'titulo' => "Nuevo libro: \"{$libro->nombre}\"",
                'tiempo' => $this->calcularTiempo($libro->created_at)
            ];
        }
        
        $ultimasDonaciones = Donacion::latest()->take(2)->get();
        foreach ($ultimasDonaciones as $donacion) {
            $actividad[] = [
                'icono' => 'fas fa-gift',
                'titulo' => "Donación de: {$donacion->nombre}",
                'tiempo' => $this->calcularTiempo($donacion->created_at)
            ];
        }
        
        $ultimosPrestamos = Prestamo::latest()->take(2)->get();
        foreach ($ultimosPrestamos as $prestamo) {
            $actividad[] = [
                'icono' => 'fas fa-hand-holding-heart',
                'titulo' => "Préstamo: {$prestamo->nombre_libro}",
                'tiempo' => $this->calcularTiempo($prestamo->created_at)
            ];
        }
        
        usort($actividad, function($a, $b) {
            return strtotime($b['tiempo']) - strtotime($a['tiempo']);
        });
        
        return array_slice($actividad, 0, 4);
    }

    private function calcularTiempo($fecha)
    {
        $carbonFecha = Carbon::parse($fecha);
        $diferencia = $carbonFecha->diffForHumans();
        
        return "Hace " . str_replace(['before', 'after'], '', $diferencia);
    }
}
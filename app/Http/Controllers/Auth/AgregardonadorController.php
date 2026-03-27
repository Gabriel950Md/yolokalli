<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
class AgregardonadorController extends Controller

{
 public function create(): view 
 {
    return view('post.donadores',[
        'posts' => Post::latest()->paginate()
    ]);
 }   


    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'gmail' => ['required', 'string', 'lowercase', 'gmail', 'max:255', 'unique:'.User::class],
            'telefono' => ['required', 'string', 'max:255'],
            'tipo_donacion' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'nombre' => $request->nombre,
            'gmail' => $request->gmail,
            'telefono' => $request->telefono,
            'tipo_donacion' => $request->tipo_donacion,
        ]);

        event(new Registered($user));

        Auth::donaciones($user);

        return redirect(route('dashboard', absolute: false));
    }
 
}

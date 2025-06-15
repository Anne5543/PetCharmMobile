<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnimalController extends Controller
{
    public function index()
    {
        $animais = Animal::where('user_id', Auth::id())->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => $animais
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'peso' => ['required', 'numeric'],
            'idade' => ['required', 'numeric'],
            'especie' => ['required', 'string', 'max:255'],
        ]);

        $animal = Animal::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Animal criado com sucesso.',
            'data' => $animal
        ], 201);
    }

    public function show($id)
    {
        $animal = Animal::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$animal) {
            return response()->json([
                'success' => false,
                'message' => 'Animal não encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $animal
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $animal = Animal::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$animal) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso não autorizado.'
            ], 403);
        }

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'peso' => ['required', 'numeric'],
            'idade' => ['required', 'numeric'],
            'especie' => ['required', 'string', 'max:255'],
        ]);

        $animal->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Animal atualizado com sucesso.',
            'data' => $animal
        ], 200);
    }

    public function destroy($id)
    {
        $animal = Animal::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$animal) {
            return response()->json([
                'success' => false,
                'message' => 'Animal não encontrado.'
            ], 404);
        }

        $animal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Animal excluído com sucesso.'
        ], 200);
    }
}

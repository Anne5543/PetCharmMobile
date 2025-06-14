<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnimalController extends Controller
{
    public readonly Animal $animals;

    public function __construct()
    {
        $this->animals = new Animal();
    }

    public function index()
    {
        $animais = $this->animals->where('user_id', Auth::id())->get();

        return response()->json([
            'success' => true,
            'data' => $animais
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'peso' => ['required', 'numeric'],
            'idade' => ['required', 'numeric'],
            'especie' => ['required', 'string'],
        ]);

        $data = $request->only(['nome', 'peso', 'idade', 'especie']);
        $data['user_id'] = Auth::id();

        $animal = Animal::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Animal criado com sucesso.',
            'data' => $animal
        ]);
    }

    public function show($id)
    {
        $animal = $this->animals->where('id', $id)->where('user_id', Auth::id())->first();

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

        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'peso' => ['required', 'numeric'],
            'idade' => ['required', 'numeric'],
            'especie' => ['required', 'string'],
        ]);

        $data = $request->only(['nome', 'peso', 'idade', 'especie']);

        $updated = $animal->update($data);

        return response()->json([
            'success' => (bool) $updated,
            'message' => $updated ? 'Animal atualizado com sucesso.' : 'Erro ao atualizar o animal.',
            'data' => $animal 
        ], $updated ? 200 : 500);
    }

    public function destroy($id)
    {
        $animal = $this->animals->where('id', $id)->where('user_id', Auth::id())->first();

        if (!$animal) {
            return response()->json([
                'success' => false,
                'message' => 'Animal não encontrado.'
            ], 404);
        }

        $deleted = $animal->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'Animal excluído com sucesso.' : 'Erro ao excluir o animal.'
        ], $deleted ? 200 : 500);
    }
}


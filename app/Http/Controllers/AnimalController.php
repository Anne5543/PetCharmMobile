<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AnimalController extends Controller
{
    public readonly Animal $animals;

    public function __construct()
    {
        $this->animals = new Animal();
    }

    /**
     * Lista todos os animais do usuário autenticado.
     */
    public function index()
    {
        $animais = $this->animals->where('user_id', Auth::id())->get();

        return response()->json([
            'success' => true,
            'data' => $animais
        ], 200);
    }

    /**
     * Cria um novo animal.
     */
    public function store(Request $request)
{
    $request->validate([
        'nome' => ['required', 'string', 'max:255'],
        'peso' => ['required', 'numeric'],
        'idade' => ['required', 'numeric'],
        'especie' => ['required', 'string'],
        'imagem' => ['nullable', 'image', 'max:2048'],
    ]);

    $data = $request->only(['nome', 'peso', 'idade', 'especie']);
    $data['user_id'] = Auth::id();

    if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
        $imagem = $request->file('imagem');
        $nomeImagem = Str::uuid() . '.' . $imagem->extension();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('SUPABASE_API_KEY'),
            'Content-Type' => $imagem->getMimeType(),
        ])->put(
            env('SUPABASE_URL') . "/storage/v1/object/" . env('SUPABASE_BUCKET') . "/{$nomeImagem}",
            file_get_contents($imagem)
        );

        if ($response->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao enviar a imagem para o Supabase.'
            ], 500);
        }

        $data['imagem'] = "https://" . parse_url(env('SUPABASE_URL'), PHP_URL_HOST) . "/storage/v1/object/public/" . env('SUPABASE_BUCKET') . "/{$nomeImagem}";
    }

    $animal = Animal::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Animal criado com sucesso.',
        'data' => $animal
    ]);
}


    /**
     * Mostra um animal específico.
     */
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

    /**
     * Atualiza um animal.
     */
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
            'imagem' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['nome', 'peso', 'idade', 'especie']);

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $imagem = $request->file('imagem');
            $nomeImagem = Str::uuid() . '.' . $imagem->extension();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_API_KEY'),
                'Content-Type' => $imagem->getMimeType(),
            ])->put(
                env('SUPABASE_URL') . "/storage/v1/object/" . env('SUPABASE_BUCKET') . "/{$nomeImagem}",
                file_get_contents($imagem)
            );

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao enviar a imagem para o Supabase.'
                ], 500);
            }

            $data['imagem'] = "https://" . parse_url(env('SUPABASE_URL'), PHP_URL_HOST) . "/storage/v1/object/public/" . env('SUPABASE_BUCKET') . "/{$nomeImagem}";
        }

        $updated = $animal->update($data);

        return response()->json([
            'success' => (bool) $updated,
            'message' => $updated ? 'Animal atualizado com sucesso.' : 'Erro ao atualizar o animal.',
            'data' => $animal 
        ], $updated ? 200 : 500);
        
    }

    /**
     * Exclui um animal.
     */
    public function destroy($id)
    {
        $animal = $this->animals->where('id', $id)->where('user_id', Auth::id())->first();

        if (!$animal) {
            return response()->json([
                'success' => false,
                'message' => 'Animal não encontrado.'
            ], 404);
        }

        if ($animal->imagem && file_exists(public_path($animal->imagem))) {
            unlink(public_path($animal->imagem));
        }

        $deleted = $animal->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Animal excluído com sucesso.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erro ao excluir o animal.'
        ], 500);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Servico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    public function index()
{
    $servicos = Servico::orderBy('id')->get(['id', 'nome']);

    if ($servicos->isEmpty()) {
        return response()->json([
            'message' => 'Nenhum serviço encontrado.',
            'data' => []
        ], 200);
    }

    return response()->json([
        'message' => 'Serviços encontrados.',
        'data' => $servicos
    ], 200);
}




    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:servicos,nome',
        ]);

        $servico = Servico::create($validated);

        return response()->json($servico, 201);
    }

    public function show(Servico $servico)
    {
        return $servico;
    }

    public function update(Request $request, Servico $servico)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:servicos,nome,' . $servico->id,
        ]);

        $servico->update($validated);

        return response()->json($servico, 200);
    }

    public function destroy(Servico $servico)
    {
        $servico->delete();

        return response()->json(null, 204);
    }
}

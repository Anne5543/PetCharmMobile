<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AgendamentoController extends Controller
{
    public function index(): JsonResponse
{
    $agendamentos = Agendamento::where('user_id', auth()->id())->get();

    return response()->json([
        'success' => true,
        'data' => $agendamentos
    ], 200);
}

public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'nome'     => 'required|string|max:255',
        'email'    => 'required|email|max:255',
        'telefone' => 'required|string|max:20',
        'data'     => 'required|date',
        'hora'     => 'required|date_format:H:i',
        'especie'  => 'required|string|max:100',
        'servico'  => 'required|string|max:255',
        'pet'      => 'required|string|max:255',
    ]);

    $validated['user_id'] = auth()->id();

    $agendamento = Agendamento::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Agendamento criado com sucesso.',
        'data' => $agendamento
    ], 201);
}

public function show(Agendamento $agendamento): JsonResponse
{
    if ($agendamento->user_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 403);
    }

    return response()->json([
        'success' => true,
        'data' => $agendamento
    ], 200);
}

public function update(Request $request, $id): JsonResponse
{
    $agendamento = Agendamento::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

    if (!$agendamento) {
        return response()->json([
            'success' => false,
            'message' => 'Acesso não autorizado.'
        ], 403);
    }

    $request->validate([
        'nome'     => 'required|string|max:255',
        'email'    => 'required|email|max:255',
        'telefone' => 'required|string|max:20',
        'data'     => 'required|date',
        'hora'     => 'required|date_format:H:i',
        'especie'  => 'required|string|max:100',
        'servico'  => 'required|string|max:255',
        'pet'      => 'required|string|max:255',
    ]);

    $data = $request->only([
        'nome', 'email', 'telefone', 'data', 'hora',
        'especie', 'servico', 'pet'
    ]);

    $updated = $agendamento->update($data);

    return response()->json([
        'success' => (bool) $updated,
        'message' => $updated
            ? 'Agendamento atualizado com sucesso.'
            : 'Erro ao atualizar o agendamento.',
        'data' => $agendamento
    ], $updated ? 200 : 500);
}

public function destroy($id): JsonResponse
{
    $agendamento = Agendamento::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

    if (!$agendamento) {
        return response()->json([
            'success' => false,
            'message' => 'Agendamento não encontrado.'
        ], 404);
    }

    $deleted = $agendamento->delete();

    return response()->json([
        'success' => (bool) $deleted,
        'message' => $deleted
            ? 'Agendamento excluído com sucesso.'
            : 'Erro ao excluir o agendamento.'
    ], $deleted ? 200 : 500);
}

}


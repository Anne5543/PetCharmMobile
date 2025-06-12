<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AgendamentoController extends Controller
{
    public function index(): JsonResponse
    {
        $agendamentos = Agendamento::with(['pet', 'servico'])->get();
        return response()->json($agendamentos);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'telefone'    => 'required|string|max:20',
            'data'        => 'required|date',
            'hora'        => 'required|date_format:H:i',
            'especie'     => 'required|string|max:100',
            'servico_id'  => 'required|exists:servicos,id',
            'pet_id'      => 'required|exists:animals,id',
        ]);

        $agendamento = Agendamento::create($validated);
        return response()->json($agendamento, 201);
    }

    public function show(Agendamento $agendamento): JsonResponse
    {
        $agendamento->load(['pet', 'servico']);
        return response()->json($agendamento);
    }

    public function update(Request $request, Agendamento $agendamento): JsonResponse
    {
        $validated = $request->validate([
            'nome'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'telefone'    => 'required|string|max:20',
            'data'        => 'required|date',
            'hora'        => 'required|date_format:H:i',
            'especie'     => 'required|string|max:100',
            'servico_id'  => 'required|exists:servicos,id',
            'pet_id'      => 'required|exists:animals,id',
        ]);

        $agendamento->update($validated);
        return response()->json($agendamento);
    }

    public function destroy(Agendamento $agendamento): JsonResponse
    {
        $agendamento->delete();
        return response()->json(null, 204);
    }
}

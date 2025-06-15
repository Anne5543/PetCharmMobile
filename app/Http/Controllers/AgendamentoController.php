<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgendamentoController extends Controller
{
    public function index(): JsonResponse
    {
        $agendamentos = Agendamento::with(['pet', 'servico'])
            ->where('user_id', Auth::id())
            ->orderBy('id')
            ->get()
            ->map(function ($agendamento) {
                return [
                    'id' => $agendamento->id,
                    'nome' => $agendamento->nome,
                    'email' => $agendamento->email,
                    'telefone' => $agendamento->telefone,
                    'data' => Carbon::parse($agendamento->data)->format('d/m/Y'),
                    'hora' => Carbon::parse($agendamento->hora)->format('H:i'),
                    'especie' => $agendamento->especie,
                    'servico' => [
                        'id' => $agendamento->servico->id ?? null,
                        'nome' => $agendamento->servico->nome ?? null,
                    ],
                    'pet' => [
                        'id' => $agendamento->pet->id ?? null,
                        'nome' => $agendamento->pet->nome ?? null,
                    ],
                    'created_at' => $agendamento->created_at,
                    'updated_at' => $agendamento->updated_at,
                ];
            });

        return response()->json([
            'message' => $agendamentos->isEmpty() ? 'Nenhum agendamento encontrado para o usuário logado.' : 'Agendamentos encontrados com sucesso.',
            'data' => $agendamentos,
        ], 200);
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

        // Verifica se o pet pertence ao usuário logado
        $pet = Animal::where('id', $validated['pet_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$pet) {
            return response()->json([
                'message' => 'Você não pode agendar com um pet que não é seu.'
            ], 403);
        }

        $agendamento = Agendamento::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        $agendamento->load(['pet', 'servico']);

        return response()->json([
            'message' => 'Agendamento criado com sucesso.',
            'data' => [
                'id' => $agendamento->id,
                'nome' => $agendamento->nome,
                'email' => $agendamento->email,
                'telefone' => $agendamento->telefone,
                'data' => Carbon::parse($agendamento->data)->format('d/m/Y'),
                'hora' => Carbon::parse($agendamento->hora)->format('H:i'),
                'especie' => $agendamento->especie,
                'servico' => [
                    'id' => $agendamento->servico->id ?? null,
                    'nome' => $agendamento->servico->nome ?? null,
                ],
                'pet' => [
                    'id' => $agendamento->pet->id ?? null,
                    'nome' => $agendamento->pet->nome ?? null,
                ],
                'created_at' => $agendamento->created_at,
                'updated_at' => $agendamento->updated_at,
            ],
        ], 201);
    }

    public function show(Agendamento $agendamento): JsonResponse
    {
        $this->authorizeUser($agendamento);

        $agendamento->load(['pet', 'servico']);

        return response()->json([
            'message' => 'Agendamento encontrado.',
            'data' => [
                'id' => $agendamento->id,
                'nome' => $agendamento->nome,
                'email' => $agendamento->email,
                'telefone' => $agendamento->telefone,
                'data' => Carbon::parse($agendamento->data)->format('d/m/Y'),
                'hora' => Carbon::parse($agendamento->hora)->format('H:i'),
                'especie' => $agendamento->especie,
                'servico' => [
                    'id' => $agendamento->servico->id ?? null,
                    'nome' => $agendamento->servico->nome ?? null,
                ],
                'pet' => [
                    'id' => $agendamento->pet->id ?? null,
                    'nome' => $agendamento->pet->nome ?? null,
                ],
                'created_at' => $agendamento->created_at,
                'updated_at' => $agendamento->updated_at,
            ],
        ], 200);
    }

    public function update(Request $request, Agendamento $agendamento): JsonResponse
    {
        $this->authorizeUser($agendamento);

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

        // Verifica se o pet pertence ao usuário logado
        $pet = Animal::where('id', $validated['pet_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$pet) {
            return response()->json([
                'message' => 'Você não pode usar um pet que não é seu.'
            ], 403);
        }

        $agendamento->update($validated);

        $agendamento->load(['pet', 'servico']);

        return response()->json([
            'message' => 'Agendamento atualizado com sucesso.',
            'data' => [
                'id' => $agendamento->id,
                'nome' => $agendamento->nome,
                'email' => $agendamento->email,
                'telefone' => $agendamento->telefone,
                'data' => Carbon::parse($agendamento->data)->format('d/m/Y'),
                'hora' => Carbon::parse($agendamento->hora)->format('H:i'),
                'especie' => $agendamento->especie,
                'servico' => [
                    'id' => $agendamento->servico->id ?? null,
                    'nome' => $agendamento->servico->nome ?? null,
                ],
                'pet' => [
                    'id' => $agendamento->pet->id ?? null,
                    'nome' => $agendamento->pet->nome ?? null,
                ],
                'created_at' => $agendamento->created_at,
                'updated_at' => $agendamento->updated_at,
            ],
        ], 200);
    }

    public function destroy(Agendamento $agendamento): JsonResponse
    {
        $this->authorizeUser($agendamento);

        $agendamento->delete();

        return response()->json([
            'message' => 'Agendamento deletado com sucesso.',
        ], 200);
    }

    protected function authorizeUser(Agendamento $agendamento): void
    {
        if ($agendamento->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este agendamento.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Domain\Exceptions\TravelOrderNotFoundException;
use App\Domain\Exceptions\TravelOrderApprovedCanceledException;
use App\Domain\Exceptions\TravelOrderUnauthorizedActionException;

use App\DTO\CreateTravelOrderDTO;
use App\DTO\ListTravelOrderDTO;
use App\DTO\ShowTravelOrderDTO;
use App\DTO\UpdateTravelOrderStatusDTO;

use App\Http\Resources\TravelOrderResource;

use App\Http\Requests\ListTravelOrderRequest;
use App\Http\Requests\ShowTravelOrderRequest;
use App\Http\Requests\StoreTravelOrderRequest;
use App\Http\Requests\UpdateTravelOrderStatusRequest;

use App\UseCases\CreateTravelOrderUseCase;
use App\UseCases\ListTravelOrderUseCase;
use App\UseCases\ShowTravelOrderUseCase;
use App\UseCases\UpdateTravelOrderStatusUseCase;

use Illuminate\Support\Facades\Log;
use \Illuminate\Http\JsonResponse;

class TravelOrderController extends Controller
{
    public function __construct(
        public readonly CreateTravelOrderUseCase $createUseCase,
        public readonly ShowTravelOrderUseCase $showUseCase,
        public readonly ListTravelOrderUseCase $listUseCase,
        public readonly UpdateTravelOrderStatusUseCase $updateUseCase
    ) {}

    public function show(ShowTravelOrderRequest $request): JsonResponse
    {
        try {
            $traveOrderShowDto = new ShowTravelOrderDTO(id: (int) $request->validated('id'));

            $travelOrder = $this->showUseCase->execute($traveOrderShowDto);
            return response()->json(['status' => true, 'message' => 'Ordem de Viagem consultada com Sucesso', 'data' => new TravelOrderResource($travelOrder)], 200);
        } catch (TravelOrderNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Não há Registros de Ordem de Viagem para o ID Informado'], 404);
        } catch (\Throwable $e) {
            Log::critical('Erro ao Consultar a Ordem de Viagem', [
                'message'  => $e->getMessage(),
                'previous' => $e->getPrevious()?->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => false, 'message' => 'Ocorreu um Erro Interno!'], 500);
        }
    }

    public function index(ListTravelOrderRequest $request)
    {
        try {
            $travelOrderDto = new ListTravelOrderDTO(
                destination: $request->validated('destination'),
                status: $request->validated('status'),
                sortDirection: $request->validated('sortDirection'),
                sortBy: $request->validated('sortBy'),
                perPage: $request->validated('perPage'),
                page: $request->validated('page'),
                departure_date_from: $request->validated('departure_date_from'),
                departure_date_to: $request->validated('departure_date_to'),
                return_date_from: $request->validated('return_date_from'),
                return_date_to: $request->validated('return_date_to'),
            );

            $results = $this->listUseCase->execute($travelOrderDto);

            return response()->json([
                'message' => 'Ordens de Viagem Consultadas com Sucesso!',
                'data'    => TravelOrderResource::collection($results->items),
                'meta'    => [
                    'total'       => $results->total,
                    'perPage'     => $results->perPage,
                    'currentPage' => $results->currentPage,
                ]
            ], 200);
        } catch (\Throwable $e) {
            Log::critical('Erro ao Consultar a Ordem de Viagem', [
                'message'  => $e->getMessage(),
                'previous' => $e->getPrevious()?->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => false, 'message' => 'Ocorreu um Erro Interno!'], 500);
        }
    }

    public function store(StoreTravelOrderRequest $request): JsonResponse
    {
        try {
            $travelOrderDto = new CreateTravelOrderDTO(
                destination: $request->validated('destination'),
                departure_date: $request->validated('departure_date'),
                return_date: $request->validated('return_date')
            );

            $travelOrder = $this->createUseCase->execute($travelOrderDto);
            return response()->json(['status' => true, 'message' => 'Ordem de Viagem criada com Sucesso', 'data' => new TravelOrderResource($travelOrder)], 201);
        } catch (\Throwable $e) {
            Log::critical('Erro ao criar Ordem de Viagem', [
                'message'  => $e->getMessage(),
                'previous' => $e->getPrevious()?->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => false, 'message' => 'Ocorreu um Erro Interno!'], 500);
        }
    }

    public function update(UpdateTravelOrderStatusRequest $request)
    {
        try {
            $updateDto   = new UpdateTravelOrderStatusDTO(id: (int) $request->validated('id'), status: $request->validated('status'));
            $travelOrder = $this->updateUseCase->execute($updateDto);

            return response()->json(['status' => true, 'message' => 'Ordem de Viagem Atualizada com Sucesso', 'data' => new TravelOrderResource($travelOrder)], 200);
        } catch (TravelOrderUnauthorizedActionException $e) {
            return response()->json(['status' => false, 'message' => 'Somente Administradores podem Alterar o Status da Ordem de Viagem'], 403);
        } catch (TravelOrderApprovedCanceledException $e) {
            return response()->json(['status' => false, 'message' => 'Não é possível alterar uma ordem de viagem já aprovada ou cancelada.'], 409);
        } catch (TravelOrderNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Id Informado para a Ordem de Viagem é Inválido'], 404);
        } catch (\Throwable $e) {
            Log::critical('Erro ao criar a Ordem de Viagem', [
                'message'  => $e->getMessage(),
                'previous' => $e->getPrevious()?->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => false, 'message' => 'Ocorreu um Erro Interno!'], 500);
        }
    }
}

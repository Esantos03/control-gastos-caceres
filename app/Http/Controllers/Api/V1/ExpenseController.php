<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreExpenseRequest;
use App\Http\Requests\Api\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public function __construct(
        private ExpenseService $expenseService
    ) {}

    /**
     * Listar gastos con filtros
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Expense::with(['category', 'subcategory', 'card', 'currency', 'merchant', 'paymentMethod']);

        // Filtros
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('card_id')) {
            $query->where('card_id', $request->card_id);
        }

        if ($request->has('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        if ($request->has('is_paid')) {
            $query->where('is_paid', $request->boolean('is_paid'));
        }

        if ($request->has('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('expense_date', $request->month)
                  ->whereYear('expense_date', $request->year);
        }

        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'expense_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', config('expenses.pagination.default_per_page'));
        $expenses = $query->paginate($perPage);

        return ExpenseResource::collection($expenses);
    }

    /**
     * Crear nuevo gasto
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $this->authorize('create', Expense::class);

        try {
            // Log para debugging - ver qué datos llegan
            Log::info('Creating expense from API', [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'validated_data' => $request->validated(),
                'card_id_in_request' => $request->input('card_id'),
                'card_id_type' => gettype($request->input('card_id')),
            ]);

            $expense = $this->expenseService->create($request->validated());

            // Log después de crear
            Log::info('Expense created', [
                'expense_id' => $expense->id,
                'card_id_saved' => $expense->card_id,
                'has_card_relation' => $expense->card !== null,
            ]);

            return response()->json([
                'message' => 'Gasto creado exitosamente',
                'data' => new ExpenseResource($expense->load(['category', 'subcategory', 'card', 'currency', 'merchant', 'paymentMethod'])),
            ], 201);
        } catch (\App\Exceptions\ExpenseException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error in ExpenseController@store', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Ocurrió un error inesperado. Por favor intente nuevamente.',
            ], 500);
        }
    }

    /**
     * Mostrar un gasto específico
     */
    public function show(Expense $expense): JsonResponse
    {
        $this->authorize('view', $expense);

        return response()->json([
            'data' => new ExpenseResource($expense->load(['category', 'subcategory', 'card', 'currency', 'merchant', 'paymentMethod', 'childExpenses'])),
        ]);
    }

    /**
     * Actualizar un gasto
     */
    public function update(UpdateExpenseRequest $request, Expense $expense): JsonResponse
    {
        $this->authorize('update', $expense);

        try {
            $expense = $this->expenseService->update($expense, $request->validated());

            return response()->json([
                'message' => 'Gasto actualizado exitosamente',
                'data' => new ExpenseResource($expense->load(['category', 'subcategory', 'card', 'currency', 'merchant', 'paymentMethod'])),
            ]);
        } catch (\App\Exceptions\ExpenseException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error in ExpenseController@update', [
                'user_id' => auth()->id(),
                'expense_id' => $expense->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Ocurrió un error inesperado. Por favor intente nuevamente.',
            ], 500);
        }
    }

    /**
     * Eliminar un gasto
     */
    public function destroy(Expense $expense): JsonResponse
    {
        $this->authorize('delete', $expense);

        try {
            // Si tiene cuotas hijas, eliminarlas también
            if ($expense->hasInstallments()) {
                $expense->childExpenses()->delete();
            }

            $expense->delete();

            Log::info('Expense deleted successfully', [
                'user_id' => auth()->id(),
                'expense_id' => $expense->id
            ]);

            return response()->json([
                'message' => 'Gasto eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting expense', [
                'user_id' => auth()->id(),
                'expense_id' => $expense->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error al eliminar el gasto',
                'error' => 'Ocurrió un error inesperado.',
            ], 500);
        }
    }

    /**
     * Generar cuotas para un gasto
     */
    public function generateInstallments(Expense $expense): JsonResponse
    {
        $this->authorize('update', $expense);

        try {
            $installments = $this->expenseService->generateInstallments($expense);

            return response()->json([
                'message' => 'Cuotas generadas exitosamente',
                'data' => ExpenseResource::collection($installments),
                'count' => count($installments),
            ]);
        } catch (\App\Exceptions\ExpenseException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error generating installments', [
                'user_id' => auth()->id(),
                'expense_id' => $expense->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Ocurrió un error inesperado al generar las cuotas.',
            ], 500);
        }
    }

    /**
     * Obtener cuotas de un gasto
     */
    public function getInstallments(Expense $expense): JsonResponse
    {
        $installments = $expense->childExpenses()->orderBy('current_installment')->get();

        return response()->json([
            'data' => ExpenseResource::collection($installments),
            'total_installments' => $expense->installments,
            'pending_installments' => $expense->pending_installments,
            'progress' => $expense->installment_progress,
        ]);
    }

    /**
     * Marcar gasto como pagado
     */
    public function markAsPaid(Expense $expense): JsonResponse
    {
        $expense->update(['is_paid' => true]);

        return response()->json([
            'message' => 'Gasto marcado como pagado',
            'data' => new ExpenseResource($expense),
        ]);
    }

    /**
     * Marcar gasto como no pagado
     */
    public function markAsUnpaid(Expense $expense): JsonResponse
    {
        $expense->update(['is_paid' => false]);

        return response()->json([
            'message' => 'Gasto marcado como no pagado',
            'data' => new ExpenseResource($expense),
        ]);
    }
}

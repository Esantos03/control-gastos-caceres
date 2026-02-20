<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCardRequest;
use App\Http\Requests\Api\UpdateCardRequest;
use App\Http\Resources\CardResource;
use App\Http\Resources\ExpenseResource;
use App\Models\Card;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CardController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Card::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $query->orderBy('name');

        return CardResource::collection($query->get());
    }

    public function store(StoreCardRequest $request): JsonResponse
    {
        $card = Card::create($request->validated());

        return response()->json([
            'message' => 'Tarjeta creada exitosamente',
            'data' => new CardResource($card),
        ], 201);
    }

    public function show(Card $card): JsonResponse
    {
        return response()->json([
            'data' => new CardResource($card),
        ]);
    }

    public function update(UpdateCardRequest $request, Card $card): JsonResponse
    {
        $card->update($request->validated());

        return response()->json([
            'message' => 'Tarjeta actualizada exitosamente',
            'data' => new CardResource($card),
        ]);
    }

    public function destroy(Card $card): JsonResponse
    {
        if ($card->expenses()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la tarjeta porque tiene gastos asociados',
            ], 422);
        }

        $card->delete();

        return response()->json([
            'message' => 'Tarjeta eliminada exitosamente',
        ]);
    }

    public function expenses(Request $request, Card $card): AnonymousResourceCollection
    {
        $query = $card->expenses()->with(['category', 'subcategory', 'currency', 'merchant', 'paymentMethod']);

        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('expense_date', $request->month)
                  ->whereYear('expense_date', $request->year);
        }

        $query->orderBy('expense_date', 'desc');

        $perPage = $request->get('per_page', config('expenses.pagination.default_per_page'));
        $expenses = $query->paginate($perPage);

        return ExpenseResource::collection($expenses);
    }

    public function usage(Request $request, Card $card): JsonResponse
    {
        $month = $request->has('month') && $request->has('year')
            ? Carbon::create($request->year, $request->month)
            : now();

        $total = $card->expenses()
            ->whereYear('expense_date', $month->year)
            ->whereMonth('expense_date', $month->month)
            ->sum('amount_converted');

        return response()->json([
            'card' => $card->name,
            'card_display' => $card->display_name,
            'last_digits' => $card->last_digits,
            'type' => $card->type,
            'credit_limit' => $card->credit_limit,
            'total_spent' => $total,
            'available' => $card->type === 'credito' ? max(0, $card->credit_limit - $total) : null,
            'usage_percentage' => $card->getCreditUsagePercentage(),
            'is_exceeded' => $card->isCreditLimitExceeded(),
            'month' => $month->format('Y-m'),
        ]);
    }

    public function toggleActive(Card $card): JsonResponse
    {
        $card->update(['is_active' => !$card->is_active]);

        return response()->json([
            'message' => 'Estado de tarjeta actualizado',
            'data' => new CardResource($card),
        ]);
    }
}

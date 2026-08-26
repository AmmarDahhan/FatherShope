<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SaleController extends Controller
{
    public function __construct(
        private SaleService $saleService,
    ) {}

    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            $sale = $this->saleService->create(
                items: $request->input('items'),
                cashierId: $request->user()->id,
                cashierName: $request->user()->username,
                paymentMethod: $request->input('payment_method'),
                tax: (float) $request->input('tax', 0),
            );

            return (new SaleResource($sale))
                ->response()
                ->setStatusCode(201);
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $sales = Sale::with('items')
            ->when($request->filled('cashier_id'), fn ($q) => $q->where('cashier_id', $request->input('cashier_id')))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return SaleResource::collection($sales);
    }

    public function show(string $id): SaleResource
    {
        $sale = Sale::with('items')->findOrFail($id);

        return new SaleResource($sale);
    }
}

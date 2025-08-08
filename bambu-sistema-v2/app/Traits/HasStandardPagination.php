<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasStandardPagination
{
    /**
     * Aplica paginación estándar con filtros y ordenamiento
     */
    protected function paginateQuery(
        Builder $query,
        Request $request,
        array $allowedSorts = ['id', 'created_at', 'updated_at'],
        array $allowedFilters = [],
        int $defaultPerPage = 25,
        int $maxPerPage = 100
    ): LengthAwarePaginator {

        // Filtros
        foreach ($allowedFilters as $filter => $column) {
            if ($request->has($filter)) {
                $value = $request->input($filter);

                if (is_array($column)) {
                    // Filtro customizado con callback
                    $column($query, $value);
                } else {
                    // Filtro simple
                    $query->where($column, $value);
                }
            }
        }

        // Búsqueda general
        if ($request->has('search') && $request->filled('search')) {
            $searchTerm = $request->input('search');
            $this->applySearch($query, $searchTerm);
        }

        // Ordenamiento
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, in_array(strtolower($order), ['asc', 'desc']) ? $order : 'desc');
        }

        // Paginación
        $perPage = min(
            (int) $request->input('per_page', $defaultPerPage),
            $maxPerPage
        );

        return $query->paginate($perPage);
    }

    /**
     * Retorna respuesta JSON paginada estándar
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator): JsonResponse
    {
        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'path' => $paginator->path(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Aplica búsqueda - debe ser implementado por cada controller
     */
    protected function applySearch(Builder $query, string $term): void
    {
        // Override en cada controller según sus campos searchables
    }
}

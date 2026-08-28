<?php

if (!function_exists('api_response')) {
    function api_response($data = [], $success = true, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}

if (!function_exists('api_list_response')) {
    /**
     * Standard envelope for paginated / searchable list endpoints.
     * Rows stay under `data`; pagination info sits in a sibling `meta` key
     * so existing clients that read `data` keep working.
     */
    function api_list_response($items = [], array $meta = [], $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $items,
            'meta'    => $meta,
        ], $code);
    }
}

if (!function_exists('api_list')) {
    /**
     * Apply an optional `search` term (against the given searchable columns,
     * which may be dot-nested relation paths like `medicine.name`) and optional
     * `per_page` / `page` pagination to an Eloquent query or relation.
     *
     * Returns ['items' => array, 'meta' => array]. When `per_page` is absent the
     * full result set is returned (backwards compatible with the old behaviour).
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation  $query
     */
    function api_list($query, array $params = [], array $searchable = []): array
    {
        $search = trim((string) ($params['search'] ?? ''));

        if ($search !== '' && !empty($searchable)) {
            $query->where(function ($q) use ($search, $searchable) {
                foreach ($searchable as $column) {
                    if (str_contains($column, '.')) {
                        $pos = strrpos($column, '.');
                        $relation = substr($column, 0, $pos);
                        $col = substr($column, $pos + 1);
                        $q->orWhereHas($relation, function ($rq) use ($col, $search) {
                            $rq->where($col, 'like', "%{$search}%");
                        });
                    } else {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        $perPage = isset($params['per_page']) ? (int) $params['per_page'] : 0;

        if ($perPage > 0) {
            $paginator = $query->paginate(min($perPage, 200));

            return [
                'items' => $paginator->items(),
                'meta'  => [
                    'total'        => $paginator->total(),
                    'per_page'     => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                ],
            ];
        }

        $items = $query->get();

        return [
            'items' => $items->all(),
            'meta'  => [
                'total'        => $items->count(),
                'per_page'     => $items->count(),
                'current_page' => 1,
                'last_page'    => 1,
            ],
        ];
    }
}

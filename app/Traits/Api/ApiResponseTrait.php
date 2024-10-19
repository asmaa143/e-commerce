<?php

namespace App\Traits\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;


trait ApiResponseTrait
{

    /**
     * @param mixed $data
     * @param int $code
     * @param null $msg
     * @param bool $status
     * @return JsonResponse
     */
    public function responseData(mixed $data, int $code = 200, $msg = null, bool $status = true): JsonResponse
    {
        return $this->handleResponse($data, $status, $code, $msg);
    }


    /**
     * @param array $data
     * @param int $code
     * @param null $msg
     * @param bool $status
     * @return JsonResponse
     */
    public function responsePaginated(array $data, int $code = 200, $msg = null, bool $status = true): JsonResponse
    {
        if ((isset($data->resource) || isset(reset($data)->resource)) && reset($data)->resource instanceof LengthAwarePaginator) {
            return $this->handlePaginatedResponse(reset($data), $status, $code, $msg);
        }
        if ((isset($data->resource) || isset(reset($data)->resource)) && reset($data)->resource instanceof CursorPaginator) {

            return $this->handleCursorPaginatedResponse(reset($data), $status, $code, $msg);
        }

        return $this->handleResponse($data, $status, $code, $msg);
    }




    /**
     * @param array $data
     * @param int $code
     * @param $msg
     * @return JsonResponse
     */
    public function responseError(array|string $data = [], int $code = 404, $msg = null): JsonResponse
    {
        return $this->handleResponse($data, false, $code, $msg);
    }


    /**
     * @param mixed $data
     * @param bool $status
     * @param int $code
     * @param null $msg
     * @param array $headers
     * @return JsonResponse
     */
    public function handleResponse(mixed $data, bool $status, int $code, $msg = null, array $headers = []): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $msg,
            'data' => $data
        ], $code, $headers);
    }

    protected function errorResponse(int $code, $msg = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $msg,
            'data' => null
        ], $code);
    }

    public function returnValidationError($validator): JsonResponse
    {

        return $this->responseError($validator->errors()->toArray(), 422, "validation error");
    }

    public function handlePaginatedResponse($data, bool $status, int $code, $msg = null, array $headers = []): JsonResponse
    {
        //Set pagination data
        $isFirst = $data->onFirstPage();
        $isLast = $data->currentPage() === $data->lastPage();
        $isNext = $data->hasMorePages();
        $isPrevious = (($data->currentPage() - 1) > 0);

        $current = $data->currentPage();
        $last = $data->lastPage();
        $next = ($isNext ? $current + 1 : null);
        $previous = ($isPrevious ? $current - 1 : null);

        //Set extra
        $extra = [
            'pagination' => [
                'meta' => [
                    'page' => [
                        "current" => $current,
                        "first" => 1,
                        "last" => $last,
                        "next" => $next,
                        "previous" => $previous,

                        "per" => $data->perPage(),
                        "from" => $data->firstItem(),
                        "to" => $data->lastItem(),

                        "count" => $data->count(),
                        "total" => $data->total(),

                        "isFirst" => $isFirst,
                        "isLast" => $isLast,
                        "isNext" => $isNext,
                        "isPrevious" => $isPrevious,
                    ],
                ],
                "links" => [
                    "path" => $data->path(),
                    "first" => $data->url(1),
                    "next" => ($isNext ? $data->url($next) : null),
                    "previous" => ($isPrevious ? $data->url($previous) : null),
                    "last" => $data->url($last),
                ],
            ],
        ];
        $response = [
            'status' => $status,
            'message' => $msg,
            'data' => $data
        ];
        //Set extra response data
        if (!!sizeof($extra)) {
            $response = $this->array_merge_recursive_distinct($response, $extra);
        }

        return response()->json($response, $code, $headers);
    }

    public function handleCursorPaginatedResponse($data, bool $status, int $code, $msg = null, array $headers = []): JsonResponse
    {

        //Set pagination data
        $isFirst = $data->onFirstPage();
//
        $isLast = $data->onLastPage();
//
//
//        $isNext = $data->hasMorePages();
//        $isPrevious = (($data->currentPage() - 1) > 0);
//
//        $current = $data->currentPage();
//        $last = $data->lastPage();
//        $next = ($isNext ? $current + 1 : null);
//        $previous = ($isPrevious ? $current - 1 : null);

        //Set extra
        $extra = [
            'pagination' => [
                'meta' => [
                    'page' => [
//                        "current" => $current,
//                        "first" => 1,
//                        "last" => $last,
//                        "next" => $next,
//                        "previous" => $previous,

                        "per" => $data->perPage(),
//                        "from" => $data->firstItem(),
//                        "to" => $data->lastItem(),

                        "count" => $data->count(),
//                        "total" => $data->total(),

                        "isFirst" => $isFirst,
                        "isLast" => $isLast,
//                        "isNext" => $isNext,
//                        "isPrevious" => $isPrevious,
                    ],
                ],
                "links" => [
//                    "path" => $data->path(),
//                    "first" => $data->url(1),
//                    "next" => ($isNext ? $data->url($next) : null),
//                    "previous" => ($isPrevious ? $data->url($previous) : null),
//                    "last" => $data->url($last),
                    "path" => $data->path(),
                    "next" => $data->nextPageUrl(),
                    "previous" => $data->previousPageUrl(),

                ],
            ],
        ];
        $response = [
            'status' => $status,
            'message' => $msg,
            'data' => $data
        ];
        //Set extra response data
        if (!!sizeof($extra)) {
            $response = $this->array_merge_recursive_distinct($response, $extra);
        }

        return response()->json($response, $code, $headers);
    }

    function array_merge_recursive_distinct(array &$array1, array &$array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}


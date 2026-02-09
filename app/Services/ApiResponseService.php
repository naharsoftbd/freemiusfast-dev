<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseService
{
    public static function success($data = [], $message = 'Success', $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'errors' => [],
        ], $code);
    }

    public static function error($message = 'Error', $errors = [], $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $code);
    }
}

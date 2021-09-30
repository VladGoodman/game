<?php

namespace App\Http\Response;

class ResponseHelper
{
    private static function basic($data, $message, $status)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    public static function custom($data, $message, $status)
    {
        return self::basic($data, $message, $status);
    }

    public static function success($data, $message)
    {
        return self::basic($data, $message, 200);
    }

    public static function created($data, $message)
    {
        return self::basic($data, $message, 201);
    }

    public static function validationError($data, $message)
    {
        return self::basic($data, $message, 422);
    }

    public static function dataError($message)
    {
        return self::basic(null, $message, 422);
    }

    public static function notFound($message)
    {
        return self::basic(null, $message, 404);
    }
}

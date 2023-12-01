<?php

namespace App\Helpers;

class ResponseHelper {
    public static function generate(bool $status, string $message, int $code, array $data = null, array $error = null) {
        $responseArray = [
            'status' => $status,
            'message' => $message,
        ];

        if ($data) {
            $responseArray = array_merge($responseArray, [
                'data' => $data,
            ]);
        }

        if ($error) {
            $responseArray = array_merge($responseArray, [
                'error' => $error,
            ]);
        }

        return response($responseArray, $code);
    }
}

<?php

namespace App\Services;

use App\Models\ConfirmCode;

class ConfirmCodeService {
    protected $confirmCode;

    /**
     * Create confirm code for the email
     *
     * @param mixed $data
     * @return ConfirmCode;
     */
    public function create($data)
    {
        try {
            return ConfirmCode::create($data);
        } catch (Throwable $e) {
            return response()->json([
                "success" => false,
                "message" => $e,
                "data" => $data
            ], 400);
        }
    }

    /**
     * Get user by user_name
     *
     * @param mixed $data
     * @return ConfirmCode;
     */
    public function find($data)
    {
        try {
            return ConfirmCode::where($data)->first();
        } catch (Throwable $e) {
            return response()->json([
                "success" => false,
                "message" => $e,
                "data" => $data
            ], 400);
        }
    }

    /**
     * Archive confirm code
     *
     * @param array $filter
     * @param array $data
     * @return ConfirmCode;
     */
    public function update($filter, $data)
    {
        try {
            return ConfirmCode::where($filter)->update($data);
        } catch (Throwable $e) {
            return response()->json([
                "success" => false,
                "message" => $e,
                "data" => $data
            ], 400);
        }
    }
}
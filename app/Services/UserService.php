<?php

namespace App\Services;

use App\Models\User;

class UserService {
    protected $user;

    /**
     * Create user
     *
     * @param mixed $data
     * @return User;
     */
    public function create($data)
    {
        try {
            return User::create($data);
        } catch (Throwable $e) {
            return response()->json([
                "success" => false,
                "message" => $e,
                "data" => $data
            ], 400);
        }
    }

    /**
     * Update user
     *
     * @param mixed $filter
     * @param mixed $data
     * @return User;
     */
    public function update($filter, $data)
    {
        try {
            return User::where($filter)->update($data);
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
     * @return User;
     */
    public function find($data)
    {
        try {
            return User::where($data)->first();
        } catch (Throwable $e) {
            return response()->json([
                "success" => false,
                "message" => "Not Found",
                "data" => $data
            ], 404);
        }
    }
}
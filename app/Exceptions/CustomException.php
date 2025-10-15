<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $this->getMessage()
            ], 400);
        }

        return back()->with('error', $this->getMessage());
    }
}
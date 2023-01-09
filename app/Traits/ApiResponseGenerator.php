<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseGenerator
{
    public bool $has_err = true;
    public string $message = '';

    public array $data = [];

    public function sendResponse(): JsonResponse
    {
        if ($this->has_err && !$this->message) {
            // setting default error message if it was not set from inside the controllers
            $this->message = 'Something went wrong';
        }

        return response()->json(['has_err' => $this->has_err, 'message' => $this->message, 'data' => $this->data]);
    }
}

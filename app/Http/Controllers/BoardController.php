<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\JsonResponse;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $boards =  Board::query()->select(['id', 'title'])->get();
        $this->has_err = false;
        $this->data['boards'] = $boards;
        return $this->sendResponse();
    }

}

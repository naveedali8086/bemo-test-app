<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColumnRequest;
use App\Http\Resources\ColumnCollection;
use App\Models\Card;
use App\Models\Column;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColumnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ColumnCollection
    {
        $columns = Column::query()
            ->where('board_id', $request->input('board_id'))
            ->where('is_deleted', 0)
            ->paginate(10);

        return new ColumnCollection($columns);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreColumnRequest $request
     * @return JsonResponse
     */
    public function store(StoreColumnRequest $request): JsonResponse
    {
        $column = Column::query()->create($request->validated());

        if ($column) {
            $this->has_err = false;
            $this->message = 'Column created.';
            $this->data['column'] = $column;
        } else {
            $this->message = 'Column could not be created, please trg again.';
        }

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Column $column
     * @return JsonResponse
     */
    public function destroy(Column $column): JsonResponse
    {
        $err_msg = 'Column could not be deleted, please try again.';

        $deleted = DB::transaction(function () use ($column, $err_msg) {
            $column_has_cards = Card::query()
                ->where('column_id', $column->id)
                ->exists();

            // if column has cards, just change column and cards' status
            if ($column_has_cards) {
                Card::where('column_id', $column->id)
                    ->update(['status' => 0]);

                $column->is_deleted = 1;
                // if column has no cards, delete card from DB
                throw_unless($column->save(), $err_msg);
            } else {
                // delete column permanently from DB if it has no cards attached
                throw_unless($column->delete(), $err_msg);
            }

            return true; // if all good
        });

        if ($deleted) {
            $this->message = 'Column deleted.';
            $this->has_err = false;
        } else {
            $this->message = $err_msg;
        }

        return $this->sendResponse();
    }
}

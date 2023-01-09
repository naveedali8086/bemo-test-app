<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Http\Resources\CardCollection;
use App\Models\Card;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): CardCollection
    {
        $cards = Card::query()
            ->where('column_id', $request->input('column_id'))
            ->when($request->input('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->input('created_at'), function ($query, $created_at) {
                return $query->whereDate('created_at', $created_at);
            })
            ->orderBy('order', 'desc')
            ->paginate(10);

        return new CardCollection($cards);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCardRequest $request
     * @return JsonResponse
     */
    public function store(StoreCardRequest $request): JsonResponse
    {
        $column_order = Card::query()
                ->where('column_id', $request->input('column_id'))
                ->max('order') + 1;

        $column = Card::query()->create(array_merge(
            $request->validated(),
            ['order' => $column_order]
        ));

        if ($column) {
            $this->has_err = false;
            $this->message = 'Card created.';
            $this->data['column'] = $column;
        } else {
            $this->message = 'Card could not be created, please trg again.';
        }

        return $this->sendResponse();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCardRequest $request
     * @param Card $card
     * @return JsonResponse
     */
    public function update(UpdateCardRequest $request, Card $card): JsonResponse
    {
        $card->fill($request->validated());

        if ($card->save()) {
            $this->message = 'Card updated.';
            $this->has_err = false;
        } else {
            $this->message = 'Card could not be updated, please trg again.';
        }

        return $this->sendResponse();
    }

    public function resetCardsOrder(Request $request): JsonResponse
    {
        $err_msg = 'Cards order could not be saved, please try again.';

        $cards = $request->input('cards');

        $updated = DB::transaction(function () use ($cards, $err_msg) {

            foreach ($cards as $card) {
                $updated = Card::query()->where('id', $card['id'])->update(['order' => $card['order']]);
                throw_unless($updated, $err_msg);
            }
            return true;

        });

        if ($updated) {
            $this->message = 'Cards order saved.';
            $this->has_err = false;
        } else {
            $this->message = $err_msg;
        }

        return $this->sendResponse();
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'transaction_id' => $this->id,
            'source_card_id' => $this->source_card_id,
            'destination_card_id' => $this->destination_card_id,
            'amount' => number_format($this->amount, 2, '.', ','),
            'created_at' => $this->created_at,
        ];
    }
}

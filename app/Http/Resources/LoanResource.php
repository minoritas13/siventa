<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'loan_date'   => $this->loan_date,
            'return_date' => $this->return_date,
            'status'      => $this->status,
            'note'        => $this->note,

            'user' => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],

            'loan_items' => $this->loanItems->map(function ($li) {
                return [
                    'id'   => $li->id,
                    'qty'  => $li->quantity,
                    'item' => [
                        'id'    => $li->item->id,
                        'name'  => $li->item->name,
                        'photo' => $li->item->photo,
                        'code' => $li->item->code,

                    ],
                ];
            }),

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}

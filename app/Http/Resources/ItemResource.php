<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'category_id' => $this->category_id,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],

            'code' => $this->code,
            'name' => $this->name,
            'photo' => $this->photo,

            'stock' => $this->stock,
            'condition' => $this->condition,
            'description' => $this->description,

            'tanggal_perolehan' => $this->tanggal_perolehan,
            'nilai_perolehan' => $this->nilai_perolehan,
            'umur_barang' => $this->umur_barang,

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

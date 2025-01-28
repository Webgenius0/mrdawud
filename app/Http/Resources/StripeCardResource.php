<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StripeCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       // return parent::toArray($request);
       return [
        'id' => $this->id,
        'card_type' => $this->card->brand ?? 'Unknown',  // Assuming the card data is available
        'last_four' => $this->card->last4 ?? 'N/A',
        'exp_month' => $this->card->exp_month ?? 'N/A',
        'exp_year' => $this->card->exp_year ?? 'N/A',
        'funding' => $this->card->funding ?? 'Unknown',
        'created' => $this->created,
    ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'affiliation'    => $this->affiliation,
            'country'        => $this->country,
            'bio'            => $this->bio,
            'locale'         => $this->locale,
            'is_super_admin' => $this->is_super_admin,
            'created_at'     => $this->created_at,
        ];
    }
}

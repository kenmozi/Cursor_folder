<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewerInvitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'email'        => $this->email,
            'status'       => $this->status,
            'is_expired'   => $this->isExpired(),
            'message'      => $this->message,
            'expires_at'   => $this->expires_at,
            'responded_at' => $this->responded_at,
            'created_at'   => $this->created_at,

            'inviter' => $this->whenLoaded('inviter', fn() => [
                'id'    => $this->inviter->id,
                'name'  => $this->inviter->name,
                'email' => $this->inviter->email,
            ]),

            'user' => $this->whenLoaded('user', fn() =>
                $this->user ? ['id' => $this->user->id, 'name' => $this->user->name] : null
            ),
        ];
    }
}

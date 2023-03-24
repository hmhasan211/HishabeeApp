<?php

namespace App\Http\Resources\api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'token' => $this->token,
            'id' =>  $this->id,
            'type' =>  $this->role_id == 2 ? 'User' : 'admin',
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar ? asset('/storage/user/' . $this->avatar) : null,
        ];
    }
}

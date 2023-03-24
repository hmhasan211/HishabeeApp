<?php

namespace App\Http\Resources\api;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function ($data) {

                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'avatar' => asset('/storage/category/' . $data->avatar ?? null),
                ];

            }),

        ];
    }
}

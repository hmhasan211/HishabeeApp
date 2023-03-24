<?php

namespace App\Http\Resources\api;

use App\Models\api\Expense;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $total = Expense::query()->User()->sum('amount');
        $totalAmount = intval($total);
        return [
            'totalCost'=>$totalAmount,
            'data' => $this->collection->transform(function ($allData) {

                return [
                    'id' => $allData->id,
                    'amount' => $allData->amount,
                    'remark' => $allData->remark,
                    'date' => $allData->date,
                    'avatar' => asset('/storage/expense/' . $allData->avatar ?? null),
                    'category' => ['id'=>$allData->category->id ,'name'=>$allData->category->name, 'avatar'=> asset('/storage/expense/' . $allData->category->avatar ?? null)],
                ];

            }),

        ];
    }
}

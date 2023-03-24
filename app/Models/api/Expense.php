<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['category_id','user_id', 'date', 'amount', 'remark', 'avatar'];

    public function scopeUser($query)
    {
        $query->where('user_id', auth()->user()->id);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}

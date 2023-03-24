<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\ExpenseCollection;
use App\Models\api\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{

    public function index(Request $request, $search=null)
    {
        $now = Carbon::now();
        $today = $request->today;
        $week = $request->week;
        $month = $request->month;
        $year = $request->year;
        $maxExp = $request->max_exp;
        $minExp = $request->min_exp;

        $query = Expense::query()->User()->with('category:id,name,avatar');

        if ($search)
        $query->whereHas('category', function ($q) use ($search) {
            $q->where('name', 'LIKE', "%$search%");
        });

        if($today){
            $query->whereDay('date',$today);
        }
        if($week){
            $query->whereBetween('date', [Carbon::now()->startOfWeek(Carbon::SUNDAY), Carbon::now()->endOfWeek(Carbon::SATURDAY)]);
        }
        if($month){
            $query->whereMonth('date',$month);
        }
        if($year){
            $query->whereYear('date',$year);
        }

        if ($maxExp){
            $data =   $query->orderBy('amount','desc')->paginate(10);
        }
        if ($minExp){
            $data =   $query->orderBy('amount','asc')->paginate(10);
        }

        $data =   $query->orderBy('id','desc')->paginate(10);

        if (!is_null($data)) {
            return new ExpenseCollection($data);
        } else {
            return response()->json(['message' => 'You dont have any data']);
        }


    }
    public function getMaximumCost()
    {
        $query = Expense::query()->User();
          $totalAmount =  $query->sum('amount');
         $maxCost =  $query->with('category:id,name,avatar')->orderBy('amount','desc')->take( 3)->get();
        $maxData = [];
      foreach ($maxCost as $data){
          $costName = $data->category->name;
          $perCost = $data['amount'];
          $percentage = round( $perCost/$totalAmount*100);
          $maxData[] = ["Name"=>$costName, "amount"=>$perCost,"percentage"=>$percentage];
      }
        if (!$maxData){
            return response()->json(['message' => "Sorry! No Data Found!"]);
        }else{
            return response()->json(['max_cost' => $maxData]);
        }

    }


    //store expsense
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'category_id' => 'numeric',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        //insert image
        $image = $request->file('avatar');
        if (isset($image)) {
            $imageName   = uniqid() . '.' . $image->getClientOriginalExtension();

            //check directory exist
            if (!Storage::disk('public')->exists('expense')) {
                Storage::disk('public')->makeDirectory('expense');
            }
            Storage::disk('public')->put('expense/' . $imageName, File::get($image));
        } else {
            $imageName = 'default.png';
        }

            Expense::create([
                'user_id' => auth()->user()->id,
                'category_id' => $request->category_id,
                'remark' => $request->remark,
                'amount' => $request->amount,
                'date' => $request->date ??  date('Y-m-d H:i:s'),
                'avatar' => $imageName,
            ]);
        return response()->json(['message' => 'Data added successfully!'], 200);
    }


    //update expsense
    public function update(Request $request,$exp)
    {
         $editExp = Expense::User()->findOrFail($exp);

        $request->validate([
            'amount' => 'required',
            'category_id' => 'numeric',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        //insert image
        $image = $request->file('avatar');
        if (isset($image)) {
            $imageName   = uniqid() . '.' . $image->getClientOriginalExtension();

            //check directory exist
            if (!Storage::disk('public')->exists('expense')) {
                Storage::disk('public')->makeDirectory('expense');
            }
            //delete old image
            if (Storage::disk('public')->exists('expense/' . $editExp->avatar)) {
                Storage::disk('public')->delete('expense/' . $editExp->avatar);
            }
            Storage::disk('public')->put('expense/' . $imageName, File::get($image));
        } else {
            $imageName = $editExp->avatar;
        }

        $editExp->update([
            'user_id' => auth()->user()->id,
            'category_id' => $request->category_id,
            'remark' => $request->remark,
            'amount' => $request->amount,
            'date' => $request->date ??  date('Y-m-d H:i:s'),
            'avatar' => $imageName,
        ]);

        return response()->json(['message' => 'data updated successfully!'], 200);
    }

    public function destroy($id)
    {
//        return 'ddd';
        Expense::User()->findOrFail($id)->delete();
        return response()->json(['message' => 'Data Deleted successfully!']);
    }
}

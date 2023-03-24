<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\api\CategoryCollection;
use App\Models\api\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
     public function index()
    {
        $data = Category::User()
                ->OrderBy('id', 'DESC')
                ->paginate(10);
    if (!is_null($data)) {
        return new CategoryCollection($data);
    } else {
        return response()->json(['message' => 'You dont have any Category']);
    }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'required', 'string', 'max:20',
                Rule::unique('categories')->where('user_id', auth()->user()->id)
                    ->where('name', $request->get('name'))
            ],
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $image = $request->file('avatar');
        $slug  = Str::slug($request->name);
        if (isset($image)) {
            //make unique name
            $imageName   = $slug . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            //check directory exist
            if (!Storage::disk('public')->exists('category')) {
                Storage::disk('public')->makeDirectory('category');
            }
            Storage::disk('public')->put('category/' . $imageName, File::get($image));
        } else {
            $imageName = 'default.png';
        }

         Category::User()->create([
             'user_id'=> auth()->user()->id,
            'name' => $request->name,
            'avatar' => $imageName,
        ]);

        return response()->json(['message' => 'Category created successfully!']);
    }


    public function update(Request $request ,$id)
    {
         $cat = Category::User()->find($id);
        $this->validate($request, [
            'name' => 'required|string|unique:categories,name,'.$cat->id,
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->file('avatar');
        $slug  = Str::slug($request->name);
        if (isset($image)) {
            //make unique name
            $imageName   = $slug . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            //check directory exist
            if (!Storage::disk('public')->exists('category')) {
                Storage::disk('public')->makeDirectory('category');
            }
            //delete old image
            if (Storage::disk('public')->exists('category/' . $cat->avatar)) {
                Storage::disk('public')->delete('category/' . $cat->avatar);
            }
            Storage::disk('public')->put('category/' . $imageName, File::get($image));
        } else {
            $imageName = 'default.png';
        }

         $cat->update([
            'name' => $request->name,
            'avatar' => $imageName
        ]);

        return response()->json(['message' => 'category updated successfully!']);
    }
}

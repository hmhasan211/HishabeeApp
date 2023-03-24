<?php



namespace App\Http\Controllers\Api;



use App\Http\Resources\api\UserResource;
use App\Models\User;


use Illuminate\Support\Str;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;



class AuthContoller extends Controller

{
    //login

    public function login(Request $request)

    {
        $inputData =  $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);



        if (Auth::attempt(['email' =>  $inputData['email'], 'password' =>  $inputData['password']]))
        {
            $user = Auth::user();

            // send Login response

            $user['token'] = $user->createToken('userToken')->plainTextToken;
            return response()->json(['data' => new UserResource($user)], 200);

        } else {

            return response([

                'message' => 'Sorry! These credentials do not match our records.'

            ], 401);

        }

    }


    //user register

    public function register(Request $request)

    {
        $input = $request->validate([

            'name' => 'required|string',

            'email' => 'nullable|email|unique:users,email',

            'password' => 'required|confirmed|min:8',

             'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg',

        ]);



        $image = $request->file('avatar');

        $slug  = Str::slug($input['name']);

        if (isset($image)) {

            //make unique name

            $imageName   = $slug . '-' . uniqid() . '.' . $image->getClientOriginalExtension();


            //check directory exist

            if (!Storage::disk('public')->exists('user')) {

                Storage::disk('public')->makeDirectory('user');

            }

            Storage::disk('public')->put('user/' . $imageName, File::get($image));

        } else {
            $imageName = 'default.png';
        }

        $user = User::create([

            'role_id' => 2,

            'name' => $input['name'],

            'email' => $input['email'],

            'password' => bcrypt($input['password']),

            'avatar' => $imageName

        ]);

        $user['token'] = $user->createToken('userToken')->plainTextToken;

        return response()->json(['data' => new UserResource($user)], 200);

    }


    //User Logout
    public function logout()
    {
        auth('sanctum')->user()->tokens()->delete();

        return response()->json(['message' => 'User logout successfully!']);

    }

}


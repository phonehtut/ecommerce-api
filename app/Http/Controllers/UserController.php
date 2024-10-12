<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::paginate(15);

            return response()->json([
                'success' => true,
                'users' => $users
            ], 200);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function detail(int $id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json([
                'success' => true,
                'user' => $user
            ], 200);
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users|max:50',
                'phone' => 'required|unique:users|max:12',
                'password' => 'required',
                'address' => 'max:500',
                'avatar' => 'image|mimes:jpeg,jpg,png'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
                'address' => $request->address,
                'avatar' => $avatarPath,
                'gender' => $request->gender
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Can't create user",
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:50',
                'email' => 'email|max:50',
                'phone' => 'unique:users|max:12',
                'address' => 'max:500',
                'avatar' => 'image|mimes:jpeg,jpg,png'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::find($id);

            if ($user) {
                $user->name = $request->name ?? $user->name;
                $user->email = $request->email ?? $user->email;
                $user->phone = $request->phone ?? $user->phone;
                $user->address = $request->address ?? $user->address;
                $user->avatar = $avatarPath ?? $user->avatar;
                $user->gender = $request->gender ?? $user->gender;
                $user->save();
            } else {
                return response()->json(['error' => 'User ID '. $id . ' Not Found'], 404);
            }

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Can't update user",
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return response()->json([
                'message' => 'User deleted successfully',
                'user' => $user
            ]);
        } else {
            return response()->json(['error' => 'User ID '. $id .' Not Found'], 404);
        }
    }
}

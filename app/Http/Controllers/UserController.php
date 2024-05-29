<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// Model Declaration
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    // View the user function : Akash
    public function index()
    {
        $users = User::with('role')->get();
        return response()->json(['users' => $users]);
    }
    // View the user form function : Akash
    public function showForm()
    {
        return view('user.user_form');
    }
    // Store the user function : Akash
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^(\+91[\-\s]?)?[0]?(91)?[789]\d{9}$/',
            'description' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = new User($request->all());
        
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = base_path('public/uploads/profile_images');
            $image->move($destinationPath, $filename);
            $user->profile_image = 'uploads/profile_images/' . $filename;
        }

        $user->save();

        return response()->json(['user' => $user->load('role')], 201);
    }

}

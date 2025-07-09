<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);

        return Inertia::render('backend/users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return Inertia::render('backend/users/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
            'dob'         => 'required|date',
            'phone'       => 'required|string|max:20',
            'address'     => 'required|string|max:500',
            'position'    => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'dob'         => $request->dob,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'position'    => $request->position,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $imageName  = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            $userData['image'] = 'uploads/users/' . $imageName;
        }

        User::create($userData);

        return redirect()->route('backend.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return Inertia::render('backend/users/Show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('backend/users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {   
        $rules = [
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'dob'         => 'required|date',
            'phone'       => 'required|string|max:20',
            'address'     => 'required|string|max:500',
            'position'    => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        $userData = [
            'name'        => $request->name,
            'email'       => $request->email,
            'dob'         => $request->dob,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'position'    => $request->position,
            'description' => $request->description,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $imageName  = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            $userData['image'] = 'uploads/users/' . $imageName;
        }

        $user->update($userData);

        return redirect()->route('backend.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function delete(User $user)
    {
        return Inertia::render('backend/users/Delete', [
            'user' => $user,
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('backend.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
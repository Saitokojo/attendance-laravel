<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('office')
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $offices = Office::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.create', compact('offices'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([...$request->validated(), 'role' => 'employee', 'is_active' => true]);

        return redirect()->route('admin.users.index')->with('success', 'Employee created.');
    }

    public function edit(User $user): View
    {
        $offices = Office::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'offices'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Employee updated.');
    }

    public function deactivate(User $user): RedirectResponse
    {
        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')->with('success', 'Employee deactivated.');
    }
}

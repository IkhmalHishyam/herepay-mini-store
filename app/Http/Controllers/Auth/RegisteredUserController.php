<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'username'          => $validated['username'],
            'email'             => $validated['email'],
            'password'          => $validated['password'],
            'role'              => UserRoleEnum::CUSTOMER->value,
            'email_verified_at' => now()
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard')
            ->with('success', 'Account created successfully! Welcome to ' . config('app.name'));
    }
}


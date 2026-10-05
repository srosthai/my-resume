<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UserProfileRequest;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The "Me" section: the single owner profile shown on the public site.
 */
class UserController extends Controller
{
    public function __construct(private readonly ImageUploadService $images) {}

    public function index(): Response
    {
        $user = User::owner()->first();

        return Inertia::render('backend/users/Index', [
            'user' => $user,
            'canDelete' => $user instanceof User && ! $user->isLastOwner(),
        ]);
    }

    public function create(): Response|RedirectResponse
    {
        if (User::owner()->exists()) {
            return $this->rejectExtraProfile();
        }

        return Inertia::render('backend/users/Create');
    }

    public function store(UserProfileRequest $request): RedirectResponse
    {
        if (User::owner()->exists()) {
            return $this->rejectExtraProfile();
        }

        $data = $request->profileData();

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'users');
        }

        $user = new User($data);
        $user->forceFill(['is_owner' => true])->save();

        return redirect()->route('backend.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user): Response
    {
        return Inertia::render('backend/users/Show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('backend/users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(UserProfileRequest $request, User $user): RedirectResponse
    {
        $data = $request->profileData();

        if ($request->hasFile('image')) {
            $this->images->delete($user->image);
            $data['image'] = $this->images->store($request->file('image'), 'users');
        }

        $user->update($data);

        return redirect()->route('backend.users.index')->with('success', 'User updated successfully.');
    }

    public function delete(User $user): Response
    {
        return Inertia::render('backend/users/Delete', [
            'user' => $user,
            'canDelete' => ! $user->isLastOwner(),
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isLastOwner()) {
            return back()->withErrors([
                'user' => 'The owner account cannot be deleted. It is the only way into the admin.',
            ]);
        }

        $this->images->delete($user->image);
        $user->delete();

        return redirect()->route('backend.users.index')->with('success', 'User deleted successfully.');
    }

    private function rejectExtraProfile(): RedirectResponse
    {
        return redirect()->route('backend.users.index')->withErrors([
            'user' => 'A profile already exists. Me is the single owner account.',
        ]);
    }
}

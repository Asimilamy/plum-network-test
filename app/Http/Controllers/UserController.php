<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;

class UserController extends Controller
{
    /**
     * The number of users listed per page when the request does not say.
     */
    protected const DEFAULT_PER_PAGE = 10;

    /**
     * Show the user management page. The table loads its rows from `list()`.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        return $this->page('Admin/Users', [
            'roles' => $this->roleOptions(),
            'perPage' => self::DEFAULT_PER_PAGE,
        ]);
    }

    /**
     * List users one page at a time.
     */
    public function list(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', User::class);

        $validated = $request->validate([
            'page' => ['integer', 'min:1'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ]);

        $users = User::query()
            ->latest('id')
            ->paginate($validated['per_page'] ?? self::DEFAULT_PER_PAGE)
            ->withQueryString();

        return UserResource::collection($users);
    }

    /**
     * Create a user with the given role.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        $user = User::create($validated);

        return response()->json(['user' => new UserResource($user)], 201);
    }

    /**
     * List the assignable roles for the create form.
     *
     * @return array<int, array{value: string, label: string}>
     */
    protected function roleOptions(): array
    {
        return array_map(
            fn (UserRole $role): array => ['value' => $role->value, 'label' => $role->label()],
            UserRole::cases(),
        );
    }
}

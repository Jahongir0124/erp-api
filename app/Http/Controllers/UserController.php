<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Override;

class UserController extends Controller implements HasMiddleware
{

    #[Override]
    public static function middleware(): array
    {
        return [
            new Middleware(
                'permission:view-user',
                only: ['index', 'show']
            ),

            new Middleware(
                'permission:create-user',
                only: ['store']
            ),
            new Middleware(
                'permission:update-user',
                only: ['update']
            ),
            new Middleware(
                'permission:delete-user',
                only: ['destroy']
            )
        ];
    }
    public function __construct(protected readonly UserService $userService) {}



    public function index(Request $request)
    {
        return UserResource::collection($this->userService->index($request));
    }

    public function store(UserStoreRequest $request)
    {
        $user = $this->userService->store($request->validated());
        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user->load('roles', 'permissions'));
    }
    public function update(
        UserUpdateRequest $request,
        User $user
        )
    {
        return new UserResource($this->userService->update($user, $request->validated()));
    }

    public function destroy(User $user)
    {
        $this->userService->destroy($user);
        return response()->json([
            'msg' => 'User deleted sucesfully'
        ]);
    }

    public function restore(int $id)
    {
        $this->userService->restore($id);
        return response()->json([
            'msg' => 'User restored succesfully'
        ]);
    }

    public function forceDelete(int $id)
    {
        $this->userService->forceDelete($id);
        return response()->json([
            'msg' => 'User permanently deleted'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\DTOs\UserData;
use App\Http\Requests\User\UpdateRoleRequest;
use App\Http\Requests\User\UserStatusRequest;
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
        $dto = UserData::fromArray($request->validated());
        $user = $this->userService->store($dto);
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
        
        $this->authorize('update', $user);
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

    public function updateRole(
        UpdateRoleRequest $request,
        User $user
    )
    {
        $this->authorize('updateRole', $user);
        $user->syncRoles($request->role);
        return new UserResource($user->fresh());
    }

    public function updateStatus(
        UserStatusRequest $request,
        User $user
    )
    {
       
        $this->authorize('updateStatus', $user);
        $user->update([
            'status' => $request->validated()['status']
        ]);
        return new UserResource($user->fresh());
    }

    
}

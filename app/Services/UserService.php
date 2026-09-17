<?php


namespace app\Services;

use App\DTOs\UserData;
use App\Http\Requests\User\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function store(UserData $data)
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password)
        ]);
        $user->assignRole($data->role);
        return $user;
    }

    public function update(object $user, array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user->fresh();
    }

    public function destroy(User $user): void
    {
        $user->delete();
    }

    public function restore(int $id): void
    {
        User::withTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        User::withTrashed()->findOrFail($id)->forceDelete();
    }

    public function index(UserRequest $request)
    {
        $query = User::query()
            ->with('roles');


        
        if ($request->filled('search')) {
            
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas("roles", function ($q) use($search){
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('role'))
            {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }

        if ($request->filled('status'))
            {
                $query->where('status', $request->status);
            }
        $sort = $request->get('sort', 'desc');
        $query->orderBy('created_at', $sort);
        return $query->paginate(10);
    }
}

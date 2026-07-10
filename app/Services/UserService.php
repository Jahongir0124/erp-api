<?php


namespace app\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function store(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->assignRole($data['role']);
        return $user;
    }

    public function update(object $user, array $data)
    {
        if (!empty($data['password']))
            {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
     
        $user->update($data);
        $user->syncRoles($data['role']);
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

    public function index($data)
    {
        $query = User::query();

        if ($data->filled('search'))
            {
                $query->where('name', 'like', '%'. $data->search . '%')
                ->orWhere('email', 'like', '%' . $data->search. '%');
            }

        if ($data->filled('role'))
            {
                $query->role($data->role);
            }

        if ($data->filled('status'))
            {
                $query->where('status', $data->status);
            }

        
        return $query->with('roles')->latest()->paginate(10);
        
    }
}
<?php



namespace app\Services;

use Spatie\Permission\Models\Role;


class RoleService
{
    public function getAll()
    {
        return Role::query()->select(['id', 'name'])->orderBy('name')->get();
    }
}
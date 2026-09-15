<?php


namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;




class RoleController extends Controller
{
    public function __construct(
        protected readonly RoleService $roleService
    )
    {}

    public function index()
    {
    
        return RoleResource::collection($this->roleService->getAll());
    }
}
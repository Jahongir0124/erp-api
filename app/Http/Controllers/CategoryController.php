<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Override;

class CategoryController extends Controller implements HasMiddleware
{
    public function __construct(protected readonly CategoryService $categoryService) {}
    #[Override]
    public static function middleware(): array
    {
        return [
            new Middleware(
                'permission:view-category'
            ),
            new Middleware(
                'permission:create-category',
                only: ['store']
            ),
            new Middleware(
                'permission:update-category',
                only: ['update']
            ),
            new Middleware(
                'permission:delete-category',
                only: ['destroy']
            )
        ];
    }
    public function index(Request $request)
    {
        return CategoryResource::collection(
            $this->categoryService->index($request)
        );
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function store(CategoryStoreRequest $request)
    {
        return new CategoryResource($this->categoryService->store($request->validated()));
    }

    public function update(
        CategoryUpdateRequest $request,
        Category $category
        )
        {
            $this->categoryService->update($request->validated(), $category);
            return response()->json([
                'msg' => "Updated Succesfully"
            ]);
        }

    public function destroy(Category $category)
    {
        $this->categoryService->destroy($category);
        return response()->json(
            [
                'msg' => 'Deleted Succesfully'
            ]
        );
    }
}

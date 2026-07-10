<?php



namespace app\Services;

use App\Models\Category;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function store(array $data): Category
    {
        return Category::create($data);
    }

    public function index(object $data)
    {
        $query = Category::query();

        if ($data->filled('search'))
            {
                $query->where('name', 'like', '%'. $data->search . '%')
                ->orWhere('description', 'like', '%'. $data->search . '%');
            }

        return $query->latest()->paginate(10);
    }

    public function update(array $data, Category $category)
    {
        $category->update($data);
        return $category->fresh();
    }

    public function destroy(Category $category): void
    {
        if ($category->products()->exists())
            {
                throw ValidationException::withMessages([
                    'category' => 'Category contains products'
                ]);
            }
        $category->delete();
    }
}
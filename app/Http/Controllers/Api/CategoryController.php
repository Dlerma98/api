<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\QueryParam;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Categories
 *
 * Managing categories
 */

#[QueryParam('page','int','The page number',example: 12)]
class CategoryController extends Controller
{
    /**
     * Get all categories
     *
     * Getting the list of the categories
     */
    public function index()
    {
        abort_if(!auth()->user()->tokenCan('categories-list'), 403);
        return CategoryResource::collection(Category::all());
    }

    #[Endpoint('Show category',description: 'Get a category by ID')]

    public function show(Category $category)
    {
        abort_if(!auth()->user()->tokenCan('categories-show'), 403);
        return new CategoryResource($category);
    }
    //
    public function list() {
        return CategoryResource::collection(Category::all());
    }

    /**
     * Store a new category
     *
     * Creating a new category
     *
     * @bodyParam name string required The name of the category. Example: Electronics
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->all();
        if($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = Str::uuid(). "." .$file->extension();
            $file->storeAs('categories', $name, 'public');
            $data['photo'] = $name;
        }
        $category = Category::create($request->all());
        return new CategoryResource($category);
    }

    public function update($category, StoreCategoryRequest $request)
    {
    $category->update($request->all());
    return new CategoryResource($category);
    }
    public function destroy(Category $category)
    {
        $category->delete();
       // return response(null, Response::HTTP_NO_CONTENT);
        return response()->noContent();
    }
}

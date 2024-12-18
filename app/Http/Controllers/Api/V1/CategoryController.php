<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Str;
use Knuckles\Scribe\Attributes\Endpoint;

/**
 * @group Categories
 *
 * Managing categories
 */

class CategoryController extends Controller
{
    /**
     * @OA\Get (
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Get List all categories",
     *     @OA\Response(
     *         response="200",
     *     description="Succesful operation",
     *
     *     ),
     *     @OA\Response(
     *          response="401",
     *      description="Unauthenticated",
     *
     *      ),
     *     @OA\Response(
     *          response="403",
     *      description="Forbidden",
     *
     *      ),
     * )
     *
     */
    public function index()
    {
        abort_if(! auth()->user()->tokenCan('categories-show-create')
            && ! auth()->user()->tokenCan('categories-list'), 403, 'no va');
        return CategoryResource::collection(Category::all());
    }

    #[Endpoint('Show category',description: 'Get a category by ID')]

    public function show(Category $category)
    {
        abort_if(! auth()->user()->tokenCan('categories-show-create'), 403, 'You do not have permission to show/create categories.');
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
        abort_if(! auth()->user()->tokenCan('categories-show-create'), 403, 'You do not have permission to show/create categories.');
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

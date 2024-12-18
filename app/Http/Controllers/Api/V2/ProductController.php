<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ProductController extends Controller
{

    public function index()
    {
        $products = Product::with('category')->paginate(9);
        return ProductResource::collection($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function list() {
        return ProductResource::collection(Category::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->all();
        if($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = Str::uuid(). "." .$file->extension();
            $file->storeAs('products', $name, 'public');
            $data['photo'] = $name;
        }
        $product = Product::create($request->all());
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        abort_if(!auth()->user()->tokenCan('products-show'), 403);
        return new CategoryResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update($product, StoreCategoryRequest $request)
    {

        $product->update($request->all());
        return new CategoryResource($product);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        // return response(null, Response::HTTP_NO_CONTENT);
        return response()->noContent();
    }


    public function getProductsByCategory($categoryId)
    {
        // Verifica si la categoría existe
        $category = Category::find($categoryId);

        if (!$category) {
            // Si no se encuentra la categoría, puedes devolver un error o una respuesta vacía
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Obtener los productos de la categoría
        $products = $category->products; // Asumiendo que tienes una relación definida en el modelo Category

        // Retornar los productos encontrados
        return response()->json($products);
    }
}

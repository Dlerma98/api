<?php

use App\Http\Controllers\Api\V2\CategoryController;
use App\Http\Controllers\Api\V2\CommentController;
use App\Http\Controllers\Api\V2\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get("lists/categories", [categoryController::class, "list"]);


//Route::get('categories', [CategoryController::class, 'index']);
//Route::get('categories/{category}', [CategoryController::class, 'show']);
//Route::post('categories', [CategoryController::class, 'store']);

//Llamado grupo de rutas para aplicar la seguridad a todas las rutas que queramos sin necesidad de ir 1 a 1
Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource("categories", categoryController::class);
    Route::apiResource("products", ProductController::class)
        ->middleware('throttle:products');

    Route::get('products/category/{categoryId}', [ProductController::class, 'getProductsByCategory']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('products/{productId}/comments', [CommentController::class, 'store']);
    Route::get('products/{productId}/comments', [CommentController::class, 'index']);
    Route::post('comments/{commentId}/reply', [CommentController::class, 'reply']);
    Route::delete('comments/{commentId}', [CommentController::class, 'destroy']);
});

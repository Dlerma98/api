<?php

namespace App\Http\Controllers\Api\V2;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Crear un comentario para un producto
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = auth()->id(); // Asumimos que el usuario está autenticado
        $comment->product_id = $product->id;
        $comment->save();

        return response()->json($comment, 201);
    }

    // Obtener todos los comentarios de un producto
    public function index($productId)
    {
        $product = Product::with('comments.replies')->findOrFail($productId);

        return response()->json($product->comments);
    }

    // Crear una respuesta a un comentario
    public function reply(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $reply = new Comment();
        $reply->content = $request->content;
        $reply->user_id = auth()->id(); // Asumimos que el usuario está autenticado
        $reply->product_id = $comment->product_id; // El mismo producto que el comentario original
        $reply->parent_id = $comment->id; // Comentario principal
        $reply->save();

        return response()->json($reply, 201);
    }

    // Eliminar un comentario
    public function destroy($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // Verificar si el usuario tiene permiso para eliminar el comentario (propietario o admin)
        if ($comment->user_id !== auth()->id() && !auth()->user()->is_admin) {
            return response()->json(['message' => 'No tienes permisos para eliminar este comentario'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comentario eliminado correctamente'], 200);
    }
}

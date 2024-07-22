<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="API Endpoints of Categories"
 * )
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories/parents",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     summary="Get all parent categories",
     *     @OA\Response(response="200", description="List of parent categories")
     * )
     */
    public function getParentCategories()
    {
        $parentCategories = Category::whereNull('category_id')->get();

        return response()->json([
            'parent_categories' => $parentCategories
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}/subcategories",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     summary="Get all subcategories by parent category ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="List of subcategories")
     * )
     */
    public function getSubcategories($id)
    {
        $subcategories = Category::where('category_id', $id)->get();

        return response()->json([
            'subcategories' => $subcategories
        ], 200);
    }
    
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     summary="Get all categories",
     *     @OA\Response(response="200", description="List of categories")
     * )
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     summary="Create a new category",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name_tm", type="string", example="test_tm"),
     *             @OA\Property(property="name_en", type="string", example="test_en"),
     *             @OA\Property(property="name_ru", type="string", example="test_ru"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="category_id", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response="201", description="Category created")
     * )
     */
    public function store(Request $request)
    {
        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     summary="Get a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Category details")
     * )
     */
    public function show(Category $category)
    {
        return $category;
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     summary="Update a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name_tm", type="string", example="test_tm"),
     *             @OA\Property(property="name_en", type="string", example="test_en"),
     *             @OA\Property(property="name_ru", type="string", example="test_ru"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="category_id", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response="200", description="Category updated")
     * )
     */
    public function update(Request $request, Category $category)
    {
        $category->update($request->all());
        return response()->json($category, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     summary="Delete a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Category deleted")
     * )
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }
}
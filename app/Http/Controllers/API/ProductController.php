<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Image;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Str;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints of Products"
 * )
 */
/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Roza Gül"),
 *     @OA\Property(property="description", type="string", example="Owadan güller"),
 *     @OA\Property(property="price", type="integer", example="85"),
 *     @OA\Property(property="discount", type="integer", example="10"),
 *     @OA\Property(property="attributes", type="integer", example="color: red, size: 40"),
 *     @OA\Property(property="code", type="integer", example="PRD123"),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="shop_id", type="integer", example=1),
 *     @OA\Property(property="brand_id", type="integer", example=1),
 *     @OA\Property(property="status", type="boolean", example=1),
 * )
 */
class ProductController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get paginated list of products",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="List of products"),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function index()
    {
        $products = Product::with(['category', 'shop', 'brand'])
            ->select('id', 'name', 'description', 'price', 'discount', 'attributes', 'code', 'category_id', 'shop_id', 'brand_id', 'status')
            ->paginate(10);

        return response()->json($products);
    }

     /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     summary="Create a new product",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Roza Gül"),
     *                 @OA\Property(property="description", type="string", example="Lorem ipsum, dolor sit amet consectetur adipisicing elit. Officiis pariatur ea laudantium molestias vero porro odio, repellat ullam. Itaque quam temporibus maxime error sunt dolorum sed perspiciatis. Reprehenderit, molestias mollitia?"),
     *                 @OA\Property(property="price", type="double", example="57"),
     *                 @OA\Property(property="discount", type="double", example="10"),
     *                 @OA\Property(property="attributes", type="double", example="12345"),
     *                 @OA\Property(property="category_id", type="integer", example="1"),
     *                 @OA\Property(property="shop_id", type="integer", example="1"),
     *                 @OA\Property(property="brand_id", type="integer", example="1"),
     *                 @OA\Property(
     *                     property="images[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="Image file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="List of products"),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required',
            'discount' => 'required',
            'attributes' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'shop_id' => 'required|exists:shops,id',
            'brand_id' => 'required|exists:brands,id',
            'images' => 'sometimes|nullable|array',
            'images.*' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $validatedData['code'] = Str::random(6);

        $product = Product::create($validatedData);
        
        $this->uploadImages($product, $validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
        ]);
    }

    // ... (other methods remain the same)

     /**
     * @OA\Post(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     summary="Update a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Updated Product Name"),
     *                 @OA\Property(property="description", type="string", example="Updated Product Description"),
     *                 @OA\Property(property="price", type="number", format="float", example=129.99),
     *                 @OA\Property(property="discount", type="number", format="float", example=15.00),
     *                 @OA\Property(property="attributes", type="string", example="{'color': 'blue', 'size': 'medium'}"),
     *                 @OA\Property(property="category_id", type="integer", example=2),
     *                 @OA\Property(property="shop_id", type="integer", example=1),
     *                 @OA\Property(property="brand_id", type="integer", example=1),
     *                 @OA\Property(property="_method", type="string", example="PUT"),
     *                 @OA\Property(
     *                     property="images[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="Image file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="Product updated",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required',
            'discount' => 'required',
            'attributes' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'shop_id' => 'required|exists:shops,id',
            'brand_id' => 'required|exists:brands,id',
            'images' => 'sometimes|nullable|array',
            'images.*' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        // Update product data
        $product->update($validatedData);

        // Handle image upload
        if ($request->hasFile('image')) {
            $this->uploadImages($product, $request->file('image'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/product/category/{category_id}",
     *     summary="Get products by category",
     *     security={{"sanctum":{}}},
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="path",
     *         description="ID of the category",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="List of products in the category",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource")),
     *             @OA\Property(property="meta", type="object", description="Pagination metadata")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *     )
     * )
     */
    public function getByCategory(Request $request, $category_id)
    {
        $perPage = $request->input('per_page', 15);

        $category = \App\Models\Category::findOrFail($category_id);

        $products = Product::where('category_id', $category_id)
                           ->with(['images', 'shop', 'brand'])
                           ->paginate($perPage);

        return ProductResource::collection($products)
                              ->additional(['meta' => [
                                  'category_name' => $category->name,
                                  'total_products' => $products->total()
                              ]]);
    }

    /**
     * @OA\Get(
     *     path="/api/product/search",
     *     summary="Search for products",
     *     security={{"sanctum":{}}},
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search query string",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource")),
     *             @OA\Property(property="meta", type="object", description="Pagination metadata")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $perPage = $request->input('per_page', 15);

        $products = Product::where('name', 'like', "%$query%")
                           ->orWhere('description', 'like', "%$query%")
                           ->with(['category', 'shop', 'brand', 'images'])
                           ->paginate($perPage);

        return ProductResource::collection($products);
    }
}
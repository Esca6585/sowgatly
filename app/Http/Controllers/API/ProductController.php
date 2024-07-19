<?php
namespace App\Http\Controllers\API;

use OpenApi\Annotations as OA;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Image;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints of Products"
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            $products = Product::with('images')->paginate(15);
            return ProductResource::collection($products);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching products'], 500);
        }
    }

     /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

     /**
     * @OA\Schema(
     *     schema="ProductRequest",
     *     required={"name", "price", "description", "discount", "attributes", "status", "category_id"},
     *     @OA\Property(property="name", type="string", example="Product Name"),
     *     @OA\Property(property="description", type="string", example="Product Description"),
     *     @OA\Property(property="price", type="number", format="float", example=99.99),
     *     @OA\Property(property="discount", type="number", format="float", example=10.00),
     *     @OA\Property(property="attributes", type="string", example="{'color': 'red', 'size': 'large'}"),
     *     @OA\Property(property="status", type="boolean", example=true),
     *     @OA\Property(property="category_id", type="integer", example=1),
     *     @OA\Property(property="images", type="array", @OA\Items(type="string", format="binary"))
     * )
     */
    /**
     * @OA\Schema(
     *     schema="Product",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Product Name"),
     *     @OA\Property(property="description", type="string", example="Product Description"),
     *     @OA\Property(property="price", type="number", format="float", example=99.99),
     *     @OA\Property(property="discount", type="number", format="float", example=10.00),
     *     @OA\Property(property="attributes", type="string", example="{'color': 'red', 'size': 'large'}"),
     *     @OA\Property(property="code", type="string", example="AbC123"),
     *     @OA\Property(property="status", type="boolean", example=true),
     *     @OA\Property(property="category_id", type="integer", example=1),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */
    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['code'] = Str::random(6);

        $product = Product::create($validatedData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        return response()->json($product, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a specific product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show($id)
    {
        $product = Product::with('images')->findOrFail($id);
        return response()->json($product);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update an existing product",
     *     security={{"sanctum":{}}},
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    /**
     * @OA\Schema(
     *     schema="ProductRequest",
     *     required={"name", "price", "description", "discount", "attributes", "status", "category_id"},
     *     @OA\Property(property="name", type="string", example="Updated Product Name"),
     *     @OA\Property(property="description", type="string", example="Updated Product Description"),
     *     @OA\Property(property="price", type="number", format="float", example=129.99),
     *     @OA\Property(property="discount", type="number", format="float", example=15.00),
     *     @OA\Property(property="attributes", type="string", example="{'color': 'blue', 'size': 'medium'}"),
     *     @OA\Property(property="status", type="boolean", example=true),
     *     @OA\Property(property="category_id", type="integer", example=2),
     *     @OA\Property(property="images", type="array", @OA\Items(type="string", format="binary"))
     * )
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric',
            'discount' => 'numeric',
            'attributes' => 'nullable|string',
            'code' => 'string|unique:products,code,' . $id,
            'status' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product->update($request->except('images'));

        if ($request->hasFile('images')) {
            foreach ($product->images as $image) {
                \Storage::disk('public')->delete($image->image);
                $image->delete();
            }

            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        return response()->json($product->load('images'));
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     security={{"sanctum":{}}},
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        foreach ($product->images as $image) {
            \Storage::disk('public')->delete($image->image);
        }

        $product->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/api/products/search",
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        $products = Product::where('name', 'like', "%{$query}%")
                           ->orWhere('description', 'like', "%{$query}%")
                           ->with('images')
                           ->get();

        return response()->json($products);
    }

    /**
     * @OA\Get(
     *     path="/api/products/category/{category_id}",
     *     summary="Get products by category",
     *     security={{"sanctum":{}}},
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     )
     * )
     */
    public function getByCategory($category_id)
    {
        $products = Product::where('category_id', $category_id)
                           ->with('images')
                           ->get();

        return response()->json($products);
    }
}
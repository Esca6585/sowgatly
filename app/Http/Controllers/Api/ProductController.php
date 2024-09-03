<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
     *     summary="Get all products",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="List of products",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ProductResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $products = Product::with(['category', 'shop', 'images', 'compositions'])->get();

            // Collect all brand IDs from all products
            $brandIds = $products->pluck('brand_ids')->flatten()->unique()->filter();

            // Fetch all brands that are associated with these products
            $brands = Brand::whereIn('id', $brandIds)->get();

            // Associate the brands with their respective products
            $products = $products->map(function ($product) use ($brands) {
                $product->brands = $brands->whereIn('id', $product->brand_ids);
                return $product;
            });

            return response()->json([
                'success' => true,
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "price", "description", "seller_status", "status", "shop_id", "category_id"},
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="price", type="number", format="float"),
     *                 @OA\Property(property="discount", type="integer"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="gender", type="string", description="Men, Women, Children and etc"),
     *                 @OA\Property(property="sizes", type="string", description="JSON string: 42, 43,...,50 yaly olcegler"),
     *                 @OA\Property(property="separated_sizes", type="string", description="JSON string: S, M, L yaly olcegler"),
     *                 @OA\Property(property="color", type="string"),
     *                 @OA\Property(property="manufacturer", type="string", description="Cykarylan yurdy"),
     *                 @OA\Property(property="width", type="number", format="float"),
     *                 @OA\Property(property="height", type="number", format="float"),
     *                 @OA\Property(property="weight", type="number", format="float", description="Hemmesi gram gorunusinde bellenmeli"),
     *                 @OA\Property(property="production_time", type="integer", description="Hemme product time minutda gorkeziler"),
     *                 @OA\Property(property="min_order", type="integer"),
     *                 @OA\Property(property="seller_status", type="boolean", description="Bu dukancy tarapyndan berilmeli status"),
     *                 @OA\Property(property="status", type="boolean", description="Bu administrator tarapyndan berilmeli status"),
     *                 @OA\Property(property="shop_id", type="integer"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="brand_ids", type="string", description="JSON string: Brand id-ler"),
     *                 @OA\Property(property="images[]", type="array", @OA\Items(type="string", format="binary"), description="Product images")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201", 
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *             @OA\Property(property="product", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422", 
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'category_id' => 'required|exists:categories,id',
            'shop_id' => 'required|exists:shops,id',
            'brand_ids' => 'nullable|array',
            'brand_ids.*' => 'exists:brands,id',
            'status' => 'required|boolean',
            'gender' => 'nullable|string',
            'sizes' => 'nullable|array',
            'separated_sizes' => 'nullable|array',
            'color' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'production_time' => 'nullable|integer',
            'min_order' => 'nullable|integer',
            'seller_status' => 'required|boolean',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string|regex:/^data:image\/[a-z]+;base64,/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $product = Product::create($request->except('images'));

            // Process and save images if provided
            if ($request->has('images') && is_array($request->images)) {
                foreach ($request->images as $imageBase64) {
                    if ($imageBase64) {
                        $image = $this->saveBase64Image($imageBase64);
                        $product->images()->create(['image_path' => $image]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product->load('images')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the product',
                'error' => $e->getMessage()
            ], 500);
        }
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
     *     @OA\Response(response="200", description="Product details"),
     *     @OA\Response(response="404", description="Product not found")
     * )
     */
    public function show($id)
    {
        try {
            $product = Product::with(['category', 'shop', 'images', 'compositions'])->find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // Check if brand_ids is already an array
            $brandIds = is_array($product->brand_ids) ? $product->brand_ids : json_decode($product->brand_ids, true);

            // Ensure $brandIds is an array and not null
            if (is_array($brandIds) && !empty($brandIds)) {
                // Fetch all brands that are associated with this product
                $brands = Brand::whereIn('id', $brandIds)->get();

                // Add the brands to the product
                $product->brands = $brands;
            } else {
                $product->brands = [];
            }

            return response()->json([
                'success' => true,
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     description="Update an existing product with new information",
     *     operationId="updateProduct",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to update",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product information",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            DB::beginTransaction();

            // Update product details
            $product->update($request->except('images'));

            // Handle image updates
            if ($request->has('images') && is_array($request->images)) {
                // Remove old images
                $product->images()->delete();

                // Add new images
                foreach ($request->images as $imageBase64) {
                    if ($imageBase64) {
                        $image = $this->saveBase64Image($imageBase64);
                        $product->images()->create(['image_path' => $image]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product->load('images')
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save a base64 encoded image and return the file path.
     *
     * @param string $base64Image
     * @return string
     */
    private function saveBase64Image($base64Image)
    {
        // Extract the image data from the base64 string
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // Generate a unique filename
        $filename = uniqid() . '.png';

        // Save the image to the storage
        Storage::disk('public')->put('product_images/' . $filename, $imageData);

        // Return the file path
        return 'product_images/' . $filename;
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Product deleted successfully"),
     *     @OA\Response(response="404", description="Product not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 200);
            }
            $product->delete();
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the product',
                'error' => $e->getMessage()
            ], 200);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/product/search",
    *     summary="Search for products",
    *     tags={"Products"},
    *     security={{"sanctum":{}}},
    *     @OA\Parameter(
    *         name="name",
    *         in="query",
    *         description="Search by product name",
    *         required=false,
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Parameter(
    *         name="min_price",
    *         in="query",
    *         description="Minimum price",
    *         required=false,
    *         @OA\Schema(type="number")
    *     ),
    *     @OA\Parameter(
    *         name="max_price",
    *         in="query",
    *         description="Maximum price",
    *         required=false,
    *         @OA\Schema(type="number")
    *     ),
    *     @OA\Parameter(
    *         name="category_id",
    *         in="query",
    *         description="Category ID",
    *         required=false,
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean"),
    *             @OA\Property(property="data", type="object"),
    *             @OA\Property(property="message", type="string")
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Server error"
    *     )
    * )
    */
    public function search(Request $request)
    {
        try {
            $query = Product::query();

            if ($request->has('name')) {
                $query->where('name', 'like', '%' . $request->input('name') . '%');
            }

            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->input('min_price'));
            }

            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->input('max_price'));
            }

            if ($request->has('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            $products = $query->with(['category', 'shop', 'images', 'compositions'])->paginate(10);

            // Fetch all brand IDs from the products
            $brandIds = $products->pluck('brand_ids')->flatten()->unique()->filter();

            // Fetch all brands that are associated with these products
            $brands = Brand::whereIn('id', $brandIds)->get();

            // Associate the brands with their respective products
            $products->getCollection()->transform(function ($product) use ($brands) {
                $product->brands = $brands->whereIn('id', $product->brand_ids);
                return $product;
            });

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching for products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/product/category/{category_id}",
     *     summary="Get products by category",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="path",
     *         description="ID of category to return products for",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function getByCategory($category_id)
    {
        try {
            $category = Category::find($category_id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $products = Product::where('category_id', $category_id)
                ->with(['category', 'shop', 'images', 'compositions'])
                ->paginate(10);

            // Fetch all brand IDs from the products
            $brandIds = $products->pluck('brand_ids')->flatten()->unique()->filter();

            // Fetch all brands that are associated with these products
            $brands = Brand::whereIn('id', $brandIds)->get();

            // Associate the brands with their respective products
            $products->getCollection()->transform(function ($product) use ($brands) {
                $product->brands = $brands->whereIn('id', $product->brand_ids);
                return $product;
            });

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching products by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
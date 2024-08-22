<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Composition;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

 /**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints for Products"
 * )
 */
/**
 * @OA\Schema(
 *     schema="Product",
 *     required={"name", "price", "shop_id", "brand_id", "category_id"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="discount", type="integer", nullable=true),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="gender", type="string", nullable=true),
 *     @OA\Property(property="sizes", type="string", nullable=true),
 *     @OA\Property(property="separated_sizes", type="string", nullable=true),
 *     @OA\Property(property="color", type="string", nullable=true),
 *     @OA\Property(property="manufacturer", type="string", nullable=true),
 *     @OA\Property(property="width", type="number", format="float", nullable=true),
 *     @OA\Property(property="height", type="number", format="float", nullable=true),
 *     @OA\Property(property="weight", type="number", format="float", nullable=true),
 *     @OA\Property(property="production_time", type="integer", nullable=true),
 *     @OA\Property(property="min_order", type="integer", nullable=true),
 *     @OA\Property(property="seller_status", type="boolean"),
 *     @OA\Property(property="status", type="boolean"),
 *     @OA\Property(property="shop_id", type="integer"),
 *     @OA\Property(property="brand_id", type="integer"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ProductResource")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $products = Product::with(['images', 'brand', 'category', 'compositions'])->paginate(15);
        return ProductResource::collection($products);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a specific product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show($id)
    {
        $product = Product::with(['images', 'brand', 'category', 'compositions'])->findOrFail($id);
        return new ProductResource($product);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'discount' => 'nullable|integer',
            'description' => 'required|string',
            'gender' => 'nullable|string',
            'sizes' => 'nullable|json',
            'separated_sizes' => 'nullable|json',
            'color' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'production_time' => 'nullable|integer',
            'min_order' => 'nullable|integer',
            'seller_status' => 'required|boolean',
            'status' => 'required|boolean',
            'shop_id' => 'required|exists:shops,id',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'compositions' => 'nullable|array',
            'compositions.*.id' => 'required|exists:compositions,id',
            'compositions.*.qty' => 'required|numeric',
            'compositions.*.qty_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $product = Product::create($request->except('compositions'));

            if ($request->has('compositions')) {
                $compositions = [];
                foreach ($request->compositions as $composition) {
                    $compositions[$composition['id']] = [
                        'qty' => $composition['qty'],
                        'qty_type' => $composition['qty_type'],
                    ];
                }
                $product->compositions()->attach($compositions);
            }

            DB::commit();

            $product->load(['images', 'brand', 'category', 'compositions']);
            return new ProductResource($product);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while creating the product.'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update an existing product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'price' => 'numeric',
            'discount' => 'nullable|integer',
            'description' => 'string',
            'gender' => 'nullable|string',
            'sizes' => 'nullable|json',
            'separated_sizes' => 'nullable|json',
            'color' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'production_time' => 'nullable|integer',
            'min_order' => 'nullable|integer',
            'seller_status' => 'boolean',
            'status' => 'boolean',
            'shop_id' => 'exists:shops,id',
            'brand_id' => 'exists:brands,id',
            'category_id' => 'exists:categories,id',
            'compositions' => 'nullable|array',
            'compositions.*.id' => 'required|exists:compositions,id',
            'compositions.*.qty' => 'required|numeric',
            'compositions.*.qty_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $product->update($request->except('compositions'));

            if ($request->has('compositions')) {
                $compositions = [];
                foreach ($request->compositions as $composition) {
                    $compositions[$composition['id']] = [
                        'qty' => $composition['qty'],
                        'qty_type' => $composition['qty_type'],
                    ];
                }
                $product->compositions()->sync($compositions);
            }

            DB::commit();

            $product->load(['images', 'brand', 'category', 'compositions']);
            return new ProductResource($product);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while updating the product.'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
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
        $product->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/api/product/search",
     *     summary="Search for products",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ProductResource")
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $query = $request->input('q');
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->with(['images', 'brand', 'category', 'compositions'])
            ->paginate(15);

        return ProductResource::collection($products);
    }

    /**
     * @OA\Get(
     *     path="/api/product/category/{category_id}",
     *     summary="Get products by category",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ProductResource")
     *             )
     *         )
     *     )
     * )
     */
    public function getByCategory($category_id)
    {
        $products = Product::where('category_id', $category_id)
            ->with(['images', 'brand', 'category', 'compositions'])
            ->paginate(15);

        return ProductResource::collection($products);
    }
    /**
     * @OA\Get(
     *     path="/api/products/featured",
     *     summary="Get featured products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     )
     * )
     */
    public function featured()
    {
        $featuredProducts = Product::where('featured', true)
            ->with(['images', 'brand', 'category', 'compositions'])
            ->take(10)
            ->get();

        return ProductResource::collection($featuredProducts);
    }

    /**
     * @OA\Get(
     *     path="/api/products/latest",
     *     summary="Get latest products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     )
     * )
     */
    public function latest()
    {
        $latestProducts = Product::latest()
            ->with(['images', 'brand', 'category', 'compositions'])
            ->take(10)
            ->get();

        return ProductResource::collection($latestProducts);
    }

    /**
     * @OA\Get(
     *     path="/api/products/discounted",
     *     summary="Get discounted products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     )
     * )
     */
    public function discounted()
    {
        $discountedProducts = Product::where('discount', '>', 0)
            ->with(['images', 'brand', 'category', 'compositions'])
            ->take(10)
            ->get();

        return ProductResource::collection($discountedProducts);
    }

    /**
     * @OA\Get(
     *     path="/api/products/by-brand/{brand_id}",
     *     summary="Get products by brand",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="brand_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function getByBrand($brand_id)
    {
        $products = Product::where('brand_id', $brand_id)
            ->with(['images', 'brand', 'category', 'compositions'])
            ->paginate(15);

        return ProductResource::collection($products);
    }

    /**
     * @OA\Post(
     *     path="/api/products/{id}/toggle-featured",
     *     summary="Toggle featured status of a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function toggleFeatured($id)
    {
        $product = Product::findOrFail($id);
        $product->featured = !$product->featured;
        $product->save();

        return new ProductResource($product);
    }
}
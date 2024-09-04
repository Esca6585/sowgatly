<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\ProductStoreRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\OpenApi\Schemas\ProductResource;
use App\OpenApi\Schemas\CategoryResource;
use App\OpenApi\Schemas\ShopResource;
use App\OpenApi\Schemas\ImageResource;
use App\OpenApi\Schemas\CompositionResource;
use App\OpenApi\Schemas\BrandResource;
use Str;
use Storage;

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
     *     summary="Create a new product with images",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name","price","description","seller_status","status","shop_id","category_id"},
     *             @OA\Property(property="name", type="string", example="Stylish T-Shirt"),
     *             @OA\Property(property="price", type="number", format="float", example=29.99),
     *             @OA\Property(property="discount", type="integer", example=10),
     *             @OA\Property(property="description", type="string", example="A comfortable and stylish t-shirt for everyday wear."),
     *             @OA\Property(property="gender", type="string", example="Unisex"),
     *             @OA\Property(property="sizes", type="array", @OA\Items(type="integer"), example={42, 43, 44, 45}),
     *             @OA\Property(property="separated_sizes", type="array", @OA\Items(type="string"), example={"S", "M", "L", "XL"}),
     *             @OA\Property(property="color", type="string", example="Blue"),
     *             @OA\Property(property="manufacturer", type="string", example="FashionCo"),
     *             @OA\Property(property="width", type="number", format="float", example=30.5),
     *             @OA\Property(property="height", type="number", format="float", example=50.0),
     *             @OA\Property(property="weight", type="number", format="float", example=200),
     *             @OA\Property(property="production_time", type="integer", example=300),
     *             @OA\Property(property="min_order", type="integer", example=1),
     *             @OA\Property(property="seller_status", type="boolean", example=true),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="shop_id", type="integer", example=1),
     *             @OA\Property(property="category_id", type="integer", example=3),
     *             @OA\Property(property="brand_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
    * @OA\Property(
    *     property="images",
    *     type="array",
    *     @OA\Items(type="string"),
    *     description="Array of base64 encoded images",
    *     example={
    *         "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==",
    *         "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==",
    *         "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==",
    *         "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==",
    *         "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg=="
    *     }
    * )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *             @OA\Property(property="product", ref="#/components/schemas/ProductResource")
     *         )
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
    public function store(ProductStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Convert array fields to JSON
            $jsonFields = ['sizes', 'separated_sizes', 'brand_ids'];
            foreach ($jsonFields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = json_encode($data[$field]);
                }
            }

            // Remove images from data array
            $images = $data['images'] ?? [];
            unset($data['images']);

            $product = Product::create($data);

            // Handle image uploads
            foreach ($images as $base64Image) {
                $imageUrl = $this->uploadBase64Image($base64Image);
                $product->images()->create(['url' => $imageUrl]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Product created successfully',
                'product' => new ProductResource($product)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
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
        $product = Product::with('images')->findOrFail($id);
        return new ProductResource($product);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update an existing product with images",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to update",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Stylish T-Shirt"),
     *             @OA\Property(property="price", type="number", format="float", example=34.99),
     *             @OA\Property(property="discount", type="integer", example=15),
     *             @OA\Property(property="description", type="string", example="An updated comfortable and stylish t-shirt for everyday wear."),
     *             @OA\Property(property="gender", type="string", example="Unisex"),
     *             @OA\Property(property="sizes", type="array", @OA\Items(type="integer"), example={42, 43, 44, 45, 46}),
     *             @OA\Property(property="separated_sizes", type="array", @OA\Items(type="string"), example={"S", "M", "L", "XL", "XXL"}),
     *             @OA\Property(property="color", type="string", example="Red"),
     *             @OA\Property(property="manufacturer", type="string", example="UpdatedFashionCo"),
     *             @OA\Property(property="width", type="number", format="float", example=31.0),
     *             @OA\Property(property="height", type="number", format="float", example=51.0),
     *             @OA\Property(property="weight", type="number", format="float", example=210),
     *             @OA\Property(property="production_time", type="integer", example=280),
     *             @OA\Property(property="min_order", type="integer", example=2),
     *             @OA\Property(property="seller_status", type="boolean", example=true),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="shop_id", type="integer", example=1),
     *             @OA\Property(property="category_id", type="integer", example=3),
     *             @OA\Property(property="brand_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3, 4}),
     *             @OA\Property(
     *                 property="images",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 description="Array of base64 encoded images",
     *                 example={
     *                     "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==",
     *                     "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg=="
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="product", ref="#/components/schemas/ProductResource")
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
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();

            // Update product details
            $product->update($request->except('images', 'brand_ids'));

            // Handle brand_ids
            if ($request->has('brand_ids')) {
                $brandIds = json_decode($request->brand_ids, true);
                $product->brands()->sync($brandIds);
            }

            // Handle images
            if ($request->has('images')) {
                foreach ($request->images as $imageBase64) {
                    if ($imageBase64) {
                        try {
                            $imagePath = $this->uploadBase64Image($imageBase64);
                            $product->images()->create(['image_path' => $imagePath]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to save image: ' . $e->getMessage());
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => new ProductResource($product->load('images', 'brands'))
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while updating the product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function uploadBase64Image($base64Image)
    {
        // Extract the image data from the base64 string
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // Generate a unique filename
        $filename = Str::random(40) . '.png';

        // Store the image
        Storage::disk('public')->put('product_images/' . $filename, $imageData);

        // Return the URL
        return 'storage/product_images/' . $filename;
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
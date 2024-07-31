<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
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
     *     @OA\Response(response="200", description="List of products")
     * )
     */
    public function index()
    {
        try {
            $products = Product::with('images')->with('shop')->paginate(15);
            return ProductResource::collection($products);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching products'], 500);
        }
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
     *                 @OA\Property(
     *                     property="images[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="Image file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="List of products")
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
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validatedData['code'] = Str::random(6);

        $product = Product::create($validatedData);
        
        $this->uploadImages($product, $validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
        ]);
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
     *         description="Product details"
     *     )
     * )
     */
    public function show(Product $product)
    {
        return new ProductResource($category);
    }

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
     *                 @OA\Property(property="shop_id", type="integer", example="1"),
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
            'images' => 'sometimes|required|array',
            'images.*' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
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

    protected function uploadImages($product, $validatedData)
    {
        if($validatedData['images']){
            $images = $validatedData['images'];
            
            foreach($images as $image){
                $date = date("d-m-Y H-i-s");
                $fileRandName = Str::random(10);
                $fileExt = $image->getClientOriginalExtension();

                $fileName = $fileRandName . '.' . $fileExt;
                
                $path = 'product/' . Str::slug($validatedData['name'] . '-' . $date ) . '/';
    
                $image->move($path, $fileName);
                
                $originalImage = $path . $fileName;

                $image = new Image;

                $image->image = $originalImage;
                $image->product_id = $product->id;

                $image->save();
            }
        }
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
    public function destroy($lang, Product $product)
    {
        try {
            DB::beginTransaction();

            $this->deleteProductImages($product);

            $product->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ], 204);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting product: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
            ], 500);
        }
    }

    protected function deleteProductImages(Product $product)
    {
        $images = $product->images; // Assuming you have an images relationship

        if ($images->isNotEmpty()) {
            $firstImage = $images->first();
            $folder = explode('/', $firstImage->image);
            
            if (isset($folder[1]) && $folder[1] != 'product-seeder') {
                $path = public_path($folder[0] . '/' . $folder[1]);
                if (File::isDirectory($path)) {
                    File::deleteDirectory($path);
                }
            }

            $images->each->delete();
        }
    }

    /**
     * @OA\Get(
     *     path="/api/product/search",
     *     summary="Search for product",
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
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        $products = Product::where('name', 'like', "%{$query}%")
                           ->orWhere('description', 'like', "%{$query}%")
                           ->with('images')
                           ->with('shop')
                           ->get();

        return ProductResource::collection($products);
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
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Category get by Category")
     * )
     */
    public function getByCategory($category_id)
    {
        $products = Product::where('category_id', $category_id)
                           ->with('images')
                           ->with('shop')
                           ->get();

        return ProductResource::collection($products);
    }
}
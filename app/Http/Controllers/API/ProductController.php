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
    public function index()
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
     *     @OA\Property(property="name", type="string", example="Product Name"),
     *     @OA\Property(property="description", type="string", example="Product Description"),
     *     @OA\Property(property="price", type="number", format="float", example=99.99),
     *     @OA\Property(property="discount", type="number", format="float", example=10.00),
     *     @OA\Property(property="attributes", type="string", example="{'color': 'red', 'size': 'large'}"),
     *     @OA\Property(property="images", type="string", example="{'color': 'red', 'size': 'large'}"),
     *     @OA\Property(property="category_id", type="integer", example=1),
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
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validatedData['code'] = Str::random(6);

        $product = Product::create($validatedData);
        
        $this->uploadImages($product, $validatedData);

        return response()->json([
            'success' => true,
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
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show(Product $product)
    {
        return ProductResource::collection([$product]);
    }

    /**
     * @OA\Post(
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
     *     @OA\Property(property="_method", type="string", example="PUT"),
     *     @OA\Property(property="name", type="string", example="Updated Product Name"),
     *     @OA\Property(property="description", type="string", example="Updated Product Description"),
     *     @OA\Property(property="price", type="number", format="float", example=129.99),
     *     @OA\Property(property="discount", type="number", format="float", example=15.00),
     *     @OA\Property(property="attributes", type="string", example="{'color': 'blue', 'size': 'medium'}"),
     *     @OA\Property(property="category_id", type="integer", example=2),
     * )
     */
    public function update(Request $request, Product $product)
    {
        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
            'request' => $request
        ], 200);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required',
            'discount' => 'required',
            'attributes' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'images' => 'sometimes|required|array',
            'images.*' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ], 200);

        $product->update($validatedData);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ], 200);

        // return response()->json([
        //     'request' => $request,
        //     'product' => $product,
        // ]);

        // $validatedData = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'description' => 'required|string',
        //     'price' => 'required',
        //     'discount' => 'required',
        //     'attributes' => 'required|string',
        //     'category_id' => 'required|exists:categories,id',
        //     'images' => 'required|array',
        //     'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        // ]);
        

        // $product->name = $validatedData['name'];
        // $product->description = $validatedData['description'];
        // $product->price = $validatedData['price'];
        // $product->discount = $validatedData['discount'];
        // $product->attributes = $validatedData['attributes'];
        // $product->code = $validatedData['code'];
        // $product->status = $validatedData['status'];
        // $product->category_id = $validatedData['category_id'];

        // $product->update();

        // $this->uploadImages($product, $validatedData);

        // return response()->json([
        //     'success' => true,
        // ]);
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
        $this->deleteFolder($product->id);

        $product->delete();

        return redirect()->route('product.index', [ app()->getlocale() ])->with('success-delete', 'The resource was deleted!');
    }

    public function deleteFolder($product_id)
    {
        $image = Image::where('product_id', $product_id)->first();
        $images = Image::where('product_id', $product_id)->get();
        
        if($image){
            $folder = explode('/', $image->image);
            
            if($folder[1] != 'product-seeder'){
                \File::deleteDirectory($folder[0] . '/' . $folder[1]);
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

        return ProductResource::collection($products);
    }
}
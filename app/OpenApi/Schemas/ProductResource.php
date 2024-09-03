<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     title="Product Resource",
 *     description="Product resource",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="discount", type="integer", nullable=true),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="gender", type="string", nullable=true),
 *     @OA\Property(property="sizes", type="array", @OA\Items(type="string"), nullable=true),
 *     @OA\Property(property="separated_sizes", type="array", @OA\Items(type="string"), nullable=true),
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
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="brand_ids", type="array", @OA\Items(type="integer"), nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="category",
 *         ref="#/components/schemas/CategoryResource"
 *     ),
 *     @OA\Property(
 *         property="shop",
 *         ref="#/components/schemas/ShopResource"
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ImageResource")
 *     ),
 *     @OA\Property(
 *         property="compositions",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/CompositionResource")
 *     ),
 *     @OA\Property(
 *         property="brands",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/BrandResource")
 *     )
 * )
 */
class ProductResource
{
}
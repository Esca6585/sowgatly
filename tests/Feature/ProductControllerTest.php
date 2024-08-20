<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $shop;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->shop = Shop::factory()->create();
        $this->category = Category::factory()->create();
    }

    public function test_index_returns_paginated_products()
    {
        Product::factory()->count(15)->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'price', 'discount', 'attributes', 'code', 'category_id', 'shop_id', 'status']
                ],
                'links',
                'meta'
            ]);

        $this->assertCount(10, $response->json('data')); // Assuming default pagination is 10
        
        // Add this line to debug the response
        $this->assertTrue(isset($response->json('data')[0]['shop_id']), "shop_id is missing from the response");
    }

    public function test_store_creates_new_product()
    {
        Storage::fake('public');

        $data = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'discount' => $this->faker->randomFloat(2, 0, 50),
            'attributes' => json_encode(['color' => 'red', 'size' => 'M']),
            'category_id' => $this->category->id,
            'shop_id' => $this->shop->id,
            'images' => [
                UploadedFile::fake()->image('product1.jpg'),
                UploadedFile::fake()->image('product2.jpg'),
            ],
        ];

        $response = $this->actingAs($this->user)->postJson('/api/products', $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Product created successfully',
            ]);

        $this->assertDatabaseHas('products', [
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'discount' => $data['discount'],
            'attributes' => $data['attributes'],
            'category_id' => $data['category_id'],
            'shop_id' => $data['shop_id'],
        ]);

        $product = Product::where('name', $data['name'])->first();
        $this->assertCount(2, $product->images);
    }

    public function test_show_returns_specific_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'description', 'price', 'discount', 'attributes', 'code', 'category_id', 'shop_id', 'status']
            ]);
    }

    public function test_update_modifies_existing_product()
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'Updated Product Name',
            'description' => 'Updated Product Description',
            'price' => 129.99,
            'discount' => 15.00,
            'attributes' => json_encode(['color' => 'blue', 'size' => 'medium']),
            'category_id' => $this->category->id,
            'shop_id' => $this->shop->id,
            '_method' => 'PUT',
            'images' => [
                UploadedFile::fake()->image('new_product.jpg'),
            ],
        ];

        $response = $this->actingAs($this->user)->postJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Product updated successfully',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'discount' => $data['discount'],
            'attributes' => $data['attributes'],
            'category_id' => $data['category_id'],
            'shop_id' => $data['shop_id'],
        ]);
    }

    public function test_destroy_deletes_product_and_associated_images()
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
        ]);
        $image = Image::factory()->create(['product_id' => $product->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_search_returns_matching_products()
    {
        $product1 = Product::factory()->create(['name' => 'Test Product']);
        $product2 = Product::factory()->create(['name' => 'Another Product']);

        $response = $this->actingAs($this->user)->getJson('/api/product/search?query=Test');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Test Product'])
            ->assertJsonMissing(['name' => 'Another Product']);
    }

    public function test_search_returns_empty_when_no_matches()
    {
        Product::factory()->create(['name' => 'Sample Product']);

        $response = $this->actingAs($this->user)->getJson('/api/product/search?query=Nonexistent');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_get_by_category_returns_products_in_category()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $product1 = Product::factory()->create(['category_id' => $category1->id]);
        $product2 = Product::factory()->create(['category_id' => $category1->id]);
        $product3 = Product::factory()->create(['category_id' => $category2->id]);

        $response = $this->actingAs($this->user)->getJson("/api/product/category/{$category1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $product1->id])
            ->assertJsonFragment(['id' => $product2->id])
            ->assertJsonMissing(['id' => $product3->id]);
    }

    public function test_get_by_category_returns_empty_for_category_with_no_products()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/product/category/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
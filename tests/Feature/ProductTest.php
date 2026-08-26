<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;
    private User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->manager()->create();
        $this->cashier = User::factory()->create();
    }

    public function test_any_user_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->actingAs($this->cashier)->getJson('/api/products');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_any_user_can_show_product(): void
    {
        $product = Product::factory()->create(['name' => 'Test Product']);

        $response = $this->actingAs($this->cashier)->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Test Product']);
    }

    public function test_manager_can_create_product(): void
    {
        $response = $this->actingAs($this->manager)->postJson('/api/products', [
            'name' => 'New Product',
            'price' => 19.99,
            'stock_quantity' => 50,
            'min_stock_alert' => 10,
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'New Product']);

        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    public function test_cashier_cannot_create_product(): void
    {
        $response = $this->actingAs($this->cashier)->postJson('/api/products', [
            'name' => 'Forbidden Product',
            'price' => 10,
            'stock_quantity' => 5,
            'min_stock_alert' => 2,
        ]);

        $response->assertForbidden();
    }

    public function test_manager_can_update_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->manager)->putJson("/api/products/{$product->id}", [
            'name' => 'New Name',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'New Name']);
    }

    public function test_manager_can_deactivate_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->manager)->deleteJson("/api/products/{$product->id}");

        $response->assertOk();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);
    }

    public function test_search_returns_matching_products(): void
    {
        Product::factory()->create(['name' => 'iPhone 15']);
        Product::factory()->create(['name' => 'Samsung Galaxy']);
        Product::factory()->create(['name' => 'USB Cable']);

        $response = $this->actingAs($this->cashier)->getJson('/api/products/search?q=iPhone');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_create_product_validates_required_fields(): void
    {
        $response = $this->actingAs($this->manager)->postJson('/api/products', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'price', 'stock_quantity', 'min_stock_alert']);
    }
}

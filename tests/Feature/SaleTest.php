<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;
    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->manager()->create();
        $this->cashier = User::factory()->create();
    }

    public function test_cashier_can_create_sale(): void
    {
        $product = Product::factory()->create([
            'price' => 10.00,
            'stock_quantity' => 50,
        ]);

        $response = $this->actingAs($this->cashier)->postJson('/api/sales', [
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'subtotal', 'tax', 'total', 'items'],
            ]);

        $this->assertDatabaseHas('sales', [
            'cashier_id' => $this->cashier->id,
            'total' => 20.00,
        ]);

        $product->refresh();
        $this->assertEquals(48, $product->stock_quantity);
    }

    public function test_sale_fails_with_insufficient_stock(): void
    {
        $product = Product::factory()->create([
            'price' => 25.00,
            'stock_quantity' => 3,
        ]);

        $response = $this->actingAs($this->cashier)->postJson('/api/sales', [
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('Insufficient stock', $response->json('message'));

        $product->refresh();
        $this->assertEquals(3, $product->stock_quantity);
    }

    public function test_stock_rollback_on_failure(): void
    {
        $product1 = Product::factory()->create([
            'price' => 10.00,
            'stock_quantity' => 50,
        ]);
        $product2 = Product::factory()->create([
            'price' => 20.00,
            'stock_quantity' => 2,
        ]);

        $response = $this->actingAs($this->cashier)->postJson('/api/sales', [
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product1->id, 'quantity' => 5],
                ['product_id' => $product2->id, 'quantity' => 10],
            ],
        ]);

        $response->assertStatus(422);

        $product1->refresh();
        $product2->refresh();
        $this->assertEquals(50, $product1->stock_quantity);
        $this->assertEquals(2, $product2->stock_quantity);
    }

    public function test_user_can_list_sales(): void
    {
        Sale::factory()->count(3)->create(['cashier_id' => $this->cashier->id]);

        $response = $this->actingAs($this->cashier)->getJson('/api/sales');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_show_sale(): void
    {
        $sale = Sale::factory()->create(['cashier_id' => $this->cashier->id]);

        $response = $this->actingAs($this->cashier)->getJson("/api/sales/{$sale->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $sale->id]);
    }

    public function test_unauthenticated_cannot_create_sale(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/sales', [
            'payment_method' => 'cash',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertUnauthorized();
    }

    public function test_sale_validates_items_required(): void
    {
        $response = $this->actingAs($this->cashier)->postJson('/api/sales', [
            'payment_method' => 'cash',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['items']);
    }
}

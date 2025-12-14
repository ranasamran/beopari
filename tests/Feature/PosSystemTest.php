<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use App\Models\Customer;
use App\Models\TaxRate;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class PosSystemTest extends TestCase
{
    // We don't use RefreshDatabase here because we want to run against the just-seeded DB state 
    // or we can use it but need to ensure roles are seeded. 
    // To be safe and isolated, we will manually handle setup.
    use RefreshDatabase;

    protected $company;
    protected $admin;
    protected $cashier;
    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed permissions
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Create Company
        $this->company = Company::create([
            'name' => 'Test Company',
            'contact' => '1234567890',
            'address' => 'Test Address',
        ]);

        // Create Admin
        $this->admin = User::factory()->create(['company_id' => $this->company->id]);
        $this->admin->assignRole('admin');

        // Create Manager
        $this->manager = User::factory()->create(['company_id' => $this->company->id]);
        $this->manager->assignRole('manager');

        // Create Cashier
        $this->cashier = User::factory()->create(['company_id' => $this->company->id]);
        $this->cashier->assignRole('cashier');
    }

    public function test_inventory_management_flow()
    {
        // 1. Create Product (Manager)
        $productData = [
            'name' => 'Test Item',
            'quantity' => 100,
            'cost_price' => 50,
            'retail_price' => 100,
            'margin' => 50,
            'type' => 1,
            'sku' => 'ITEM-001',
            'barcode' => '123456789',
            'reorder_point' => 10
        ];

        $response = $this->actingAs($this->manager)
            ->postJson('/api/products', $productData);
        
        $response->assertStatus(201);
        $productId = $response->json('data.id');

        // 2. Lookup by Barcode (Any Auth User)
        $this->actingAs($this->cashier)
            ->getJson('/api/products/barcode/lookup?barcode=123456789')
            ->assertStatus(200)
            ->assertJsonPath('data.id', $productId);
    }

    public function test_full_order_processing_flow()
    {
        // Setup: Product, Tax, Customer
        $product = Product::forceCreate([
            'company_id' => $this->company->id,
            'name' => 'Coke',
            'quantity' => 50,
            'cost_price' => 10,
            'retail_price' => 20, // Price is 20
            'margin' => 50,
            'type' => 1,
            'barcode' => 'COKE123'
        ]);

        $customer = Customer::create([
            'company_id' => $this->company->id,
            'name' => 'John Doe'
        ]);

        $taxRate = TaxRate::create([
            'company_id' => $this->company->id,
            'name' => 'VAT',
            'rate' => 10.00, // 10%
            'type' => 'percentage'
        ]);

        // 1. Create Order (Cashier)
        // 2 items * 20 = 40. Tax 10% = 4. Total = 44.
        $orderData = [
            'customer_id' => $customer->id,
            'tax_rate_id' => $taxRate->id,
            'details' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 20
                ]
            ],
            'payments' => [
                [
                    'method' => 'cash',
                    'amount' => 44
                ]
            ]
        ];

        $response = $this->actingAs($this->cashier)
            ->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonPath('data.subtotal', '40.00')
            ->assertJsonPath('data.tax_amount', '4.00')
            ->assertJsonPath('data.payable', '44.00') // Changed from 'total' to 'payable' based on OrderController logic
            ->assertJsonPath('data.status', 'completed');

        $orderId = $response->json('data.id');

        // 2. Verify Inventory Deduction
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 48 // 50 - 2
        ]);

        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $product->id,
            'quantity' => -2,
            'type' => 'sale'
        ]);

        // 3. Void Order (Manager) - Cashier should not be able to void (assuming permission check works)
        $this->actingAs($this->cashier)
            ->postJson("/api/orders/{$orderId}/void")
            ->assertStatus(403); // Forbidden

        $this->actingAs($this->manager)
            ->postJson("/api/orders/{$orderId}/void")
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'void');

        // 4. Verify Stock Restoration
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 50 // Restored
        ]);
        
        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $product->id,
            'quantity' => 2,
            'type' => 'return' // Logic uses addStock which might map to purchase/return
        ]);
    }

    public function test_insufficient_stock_validation()
    {
        $product = Product::forceCreate([
            'company_id' => $this->company->id,
            'name' => 'Limited Item',
            'quantity' => 5,
            'cost_price' => 10,
            'retail_price' => 20,
            'margin' => 50,
            'type' => 1
        ]);

        $customer = Customer::create(['company_id' => $this->company->id, 'name' => 'Guest']);

        $orderData = [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10, // Requesting more than available
                    'price' => 20
                ]
            ],
            'payments' => [['method' => 'cash', 'amount' => 200]]
        ];

        $this->actingAs($this->cashier)
            ->postJson('/api/orders', $orderData)
            ->assertStatus(500); // Or 422 depending on how exception is handled. Service throws standard Exception, might be 500.
    }
}

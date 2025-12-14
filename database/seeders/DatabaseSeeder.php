<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payee;
use App\Models\PayeeTrans;
use App\Models\Bank;
use App\Models\BankTrans;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 3 companies
        $companies = Company::factory(3)->create();

        // For each company, create users, products, orders, payees, banks, and related data
        foreach ($companies as $company) {
            // Users
            \App\Models\User::factory(3)->create(['company_id' => $company->id]);

            // Products
            $products = Product::factory(10)->create(['company_id' => $company->id]);

            // Orders
            $orders = Order::factory(5)->create(['company_id' => $company->id]);

            // OrderDetails
            foreach ($orders as $order) {
                foreach ($products->random(3) as $product) {
                    OrderDetail::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'name' => $product->name,
                    ]);
                }
            }

            // Payees
            $payees = Payee::factory(4)->create(['company_id' => $company->id]);

            // PayeeTrans
            foreach ($payees as $payee) {
                PayeeTrans::factory(2)->create([
                    'cus_id' => $payee->id,
                ]);
            }

            // Banks
            $banks = Bank::factory(2)->create(['company_id' => $company->id]);

            // BankTrans
            foreach ($banks as $bank) {
                BankTrans::factory(2)->create([
                    'bank_id' => $bank->id,
                    'cus_id' => $payees->random()->id,
                ]);
            }
        }
    }
}

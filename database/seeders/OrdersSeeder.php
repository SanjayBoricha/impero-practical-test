<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(FastExcel $fastExcel)
    {
        $collection = $fastExcel->import(public_path('xlsx\Practical-OrderData.xlsx'));

        $orders = collect([]);

        foreach ($collection as $order) {
            $orders->push([
                'order_id' => $order['orderId'],
                'pin_type' => $order['pinTypeId'],
                'payment_type' => $order['paymenttype'],
                'customer_name' => $order['customerName'],
                'full_address' => $order['fullAddress'],
                'order_date' => $order['orderdate'],
                'price' => $order['price'],
                'quantity' => $order['quantity'],
                'product_name' => $order['productname'],
                'created_at' => $order['orderdate'],
                'updated_at' => $order['orderdate'],
            ]);
        }

        foreach ($orders->chunk(1000) as $chunked) {
            Order::insert($chunked->toArray());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\PaymentTypes;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $firstOrder = Order::orderBy('order_date', 'ASC')->first();
        $lastOrder = Order::orderBy('order_date', 'DESC')->first();

        $rangeStart = $firstOrder ? Carbon::parse($firstOrder->order_date)->format('m-d-Y') : Carbon::now()->yesterday()->format('m-d-Y');
        $rangeEnd = $lastOrder ? Carbon::parse($lastOrder->order_date)->format('m-d-Y') : Carbon::now()->tomorrow()->format('m-d-Y');

        $productNames = Order::groupBy('product_name')->pluck('product_name');

        $paymentTypes = PaymentTypes::getKeys([1, 2, 3, 5, 6]);

        return view('home', compact('rangeStart', 'rangeEnd', 'productNames', 'paymentTypes'));
    }

    public function datatable(Request $request)
    {
        $datatable = Datatables::eloquent(Order::query())
            ->addColumn('pin_type', function ($order) {
                return $order->pin_type->description;
            })
            ->addColumn('payment_type', function ($order) {
                return $order->payment_type->description;
            })
            ->filter(function ($query) {
                $this->useFilter($query);
            })
            ->make(true);

        $tableData = $datatable->original;

        $allOrders = Order::select('payment_type', 'quantity', 'price')->where(function ($query) {
            $this->useFilter($query);
        })->get();

        $cashTotal = 0;
        $chequeTotal = 0;
        $onlineTotal = 0;
        $paymentDueTotal = 0;
        $webPaymentTotal = 0;

        foreach ($allOrders as $order) {
            switch ($order->payment_type->key) {
                case 'cash':
                    $cashTotal += ($order->quantity * $order->price);
                    break;

                case 'cheque':
                    $chequeTotal += ($order->quantity * $order->price);
                    break;

                case 'online':
                    $onlineTotal += ($order->quantity * $order->price);
                    break;

                case 'paymentDue':
                    $paymentDueTotal += ($order->quantity * $order->price);
                    break;

                case 'webPayment':
                    $webPaymentTotal += ($order->quantity * $order->price);
                    break;
            }
        }

        $totals = [];
        $totals['cashTotal'] = $cashTotal;
        $totals['chequeTotal'] = $chequeTotal;
        $totals['onlineTotal'] = $onlineTotal;
        $totals['paymentDueTotal'] = $paymentDueTotal;
        $totals['webPaymentTotal'] = $webPaymentTotal;

        $tableData['totals'] = $totals;

        $grandTotal = $cashTotal + $chequeTotal + $onlineTotal + $paymentDueTotal + $webPaymentTotal;

        $percentages = [];
        $percentages['cashPercentage'] = ($cashTotal / $grandTotal) * 100;
        $percentages['chequePercentage'] = ($chequeTotal / $grandTotal) * 100;
        $percentages['onlinePercentage'] = ($onlineTotal / $grandTotal) * 100;
        $percentages['paymentDuePercentage'] = ($paymentDueTotal / $grandTotal) * 100;
        $percentages['webPaymentPercentage'] = ($webPaymentTotal / $grandTotal) * 100;

        $tableData['percentages'] = $percentages;

        return $tableData;
    }

    public function useFilter($query)
    {
        if (!empty(request()->get('range'))) {
            $dates = explode(' - ', request()->get('range'));
            $query->whereBetween('order_date', [Carbon::createFromFormat('m/d/Y', $dates[0])->startOf('day'), Carbon::createFromFormat('m/d/Y', $dates[1])->endOf('day')]);
        }

        if (!empty(request()->get('search'))) {
            $searchTerm = request()->get('search');
            $query->where(function ($q) use ($searchTerm) {
                return $q->where('customer_name', 'like', "%{$searchTerm}%")
                    ->orWhere('full_address', 'like', "%{$searchTerm}%")
                    ->orWhere('product_name', 'like', "%{$searchTerm}%");
            });
        }

        $products = request()->get('products', []);
        if (count($products) > 0) {
            $query->whereIn('product_name', $products);
        }
    }
}

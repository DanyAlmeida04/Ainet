<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TshirtImage;

class DashboardController extends Controller
{
    public function index()
    {
        // Total sales (closed orders)
        $totalSales = Order::where('status', 'closed')->sum('total_price');

        // Total orders and counts by status
        $totalOrders = Order::count();
        $ordersByStatus = Order::selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        // Recent orders
        $recentOrders = Order::with('items')->orderBy('date', 'desc')->limit(10)->get();

        // Top tshirts by quantity sold (based on order_items)
        $topTshirts = OrderItem::selectRaw('tshirt_image_id, sum(qty) as total_qty')
            ->groupBy('tshirt_image_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $img = TshirtImage::find($row->tshirt_image_id);
                return [
                    'image' => $img,
                    'qty' => $row->total_qty,
                ];
            });

        return view('admin.dashboard', compact('totalSales', 'totalOrders', 'ordersByStatus', 'recentOrders', 'topTshirts'));
    }
}

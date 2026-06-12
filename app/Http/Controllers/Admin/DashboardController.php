<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TshirtImage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // Monthly orders/sales for the last 6 months (labels, counts, sums)
        $months = [];
        $counts = [];
        $sums = [];
        $start = Carbon::now()->startOfMonth()->subMonths(5);

        // Query grouped by year-month (works with SQLite strftime)
        $raw = Order::selectRaw("strftime('%Y-%m', date) as ym, count(*) as cnt, sum(total_price) as sum")
            ->where('date', '>=', $start->toDateString())
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->startOfMonth()->subMonths($i);
            $label = $m->format('Y-m');
            $months[] = $m->format('M Y');
            if (isset($raw[$label])) {
                $counts[] = (int) $raw[$label]->cnt;
                $sums[] = (float) $raw[$label]->sum;
            } else {
                $counts[] = 0;
                $sums[] = 0.0;
            }
        }

        // Prepare top tshirts labels and values
        $topLabels = $topTshirts->map(function($t){ return $t['image'] ? ($t['image']->name ?? 'Design #'.$t['image']->id) : 'Design'; })->toArray();
        $topValues = $topTshirts->map(function($t){ return (int) $t['qty']; })->toArray();

        return view('admin.dashboard', compact('totalSales', 'totalOrders', 'ordersByStatus', 'recentOrders', 'topTshirts', 'months', 'counts', 'sums', 'topLabels', 'topValues'));
    }

    /**
     * Return JSON stats for charts.
     * Accepts ?range=6months|lifetime|month|week
     */
    public function stats(Request $request)
    {
        $range = $request->get('range', '6months');
        $now = Carbon::now();

        // Determine date window
        switch ($range) {
            case 'week':
                $start = $now->startOfWeek();
                $groupFmt = "%Y-%m-%d"; // by day
                $labels = [];
                for ($i=0;$i<7;$i++) { $d = $start->copy()->addDays($i); $labels[] = $d->format('d M'); }
                break;
            case 'month':
                $start = $now->startOfMonth();
                $days = $now->daysInMonth;
                $labels = [];
                for ($i=0;$i<$days;$i++) { $d = $start->copy()->addDays($i); $labels[] = $d->format('d M'); }
                break;
            case 'lifetime':
                $start = null;
                $groupFmt = "%Y-%m"; // by month
                $labels = null;
                break;
            case '6months':
            default:
                $start = $now->startOfMonth()->subMonths(5);
                $groupFmt = "%Y-%m";
                $labels = [];
                for ($i = 5; $i >= 0; $i--) { $m = $now->copy()->startOfMonth()->subMonths($i); $labels[] = $m->format('M Y'); }
                break;
        }

        // Orders by status in window
        $ordersQ = Order::query();
        if ($start) { $ordersQ->where('date', '>=', $start->toDateString()); }
        $ordersByStatus = $ordersQ->selectRaw("status, count(*) as cnt")->groupBy('status')->pluck('cnt','status')->toArray();

        // Monthly (or grouped) counts and sums
        if ($range === 'week' || $range === 'month') {
            // group by day
            $fmt = '%Y-%m-%d';
            $raw = Order::selectRaw("strftime('%Y-%m-%d', date) as ym, count(*) as cnt, sum(total_price) as sum")
                ->where('date', '>=', $start->toDateString())
                ->groupBy('ym')
                ->orderBy('ym')
                ->get()
                ->keyBy('ym');

            $counts = [];$sums = [];
            foreach ($labels as $i => $lbl) {
                $d = ($start->copy()->addDays($i))->format('Y-m-d');
                $counts[] = isset($raw[$d]) ? (int)$raw[$d]->cnt : 0;
                $sums[] = isset($raw[$d]) ? (float)$raw[$d]->sum : 0.0;
            }
        } elseif ($range === 'lifetime') {
            $raw = Order::selectRaw("strftime('%Y-%m', date) as ym, count(*) as cnt, sum(total_price) as sum")
                ->groupBy('ym')->orderBy('ym')->get();
            $labels = $raw->pluck('ym')->map(function($v){ return Carbon::createFromFormat('Y-m', $v)->format('M Y'); })->toArray();
            $counts = $raw->pluck('cnt')->map(fn($v)=>(int)$v)->toArray();
            $sums = $raw->pluck('sum')->map(fn($v)=>(float)$v)->toArray();
        } else {
            // 6months default
            $raw = Order::selectRaw("strftime('%Y-%m', date) as ym, count(*) as cnt, sum(total_price) as sum")
                ->where('date', '>=', $start->toDateString())
                ->groupBy('ym')->orderBy('ym')->get()->keyBy('ym');
            $counts = [];$sums = [];
            for ($i = 5; $i >= 0; $i--) {
                $m = $now->copy()->startOfMonth()->subMonths($i);
                $k = $m->format('Y-m');
                $counts[] = isset($raw[$k]) ? (int)$raw[$k]->cnt : 0;
                $sums[] = isset($raw[$k]) ? (float)$raw[$k]->sum : 0.0;
            }
        }

        // Top tshirts in window
        $itemsQ = OrderItem::query()->selectRaw('tshirt_image_id, sum(qty) as total_qty')
            ->groupBy('tshirt_image_id')
            ->orderByDesc('total_qty');
        if ($start) {
            $itemsQ->join('orders','orders.id','=','order_items.order_id')->where('orders.date','>=',$start->toDateString());
        }
        $items = $itemsQ->limit(10)->get();
        $topLabels = [];$topValues = [];$topImages = [];
        foreach ($items as $row) {
            $img = TshirtImage::find($row->tshirt_image_id);
            $topLabels[] = $img ? ($img->name ?? 'Design #'.$img->id) : 'Design';
            $topValues[] = (int)$row->total_qty;
            // return image filename (frontend will resolve to /storage/tshirt_images/<file>)
            $topImages[] = $img ? ($img->image_url ?? 'default.png') : 'default.png';
        }

        return response()->json([
            'ordersByStatus' => $ordersByStatus,
            'labels' => $labels,
            'counts' => $counts,
            'sums' => $sums,
            'topLabels' => $topLabels,
            'topValues' => $topValues,
            'topImages' => $topImages,
        ]);
    }
}

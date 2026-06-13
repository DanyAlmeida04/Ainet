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

        // Monthly orders/sales for lifetime (labels, counts, sums) (Requisito: lifetime default)
        $raw = Order::selectRaw("strftime('%Y-%m', date) as ym, count(*) as cnt, sum(total_price) as sum")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $months = $raw->pluck('ym')->map(function($v){
            try {
                return Carbon::createFromFormat('Y-m', $v)->format('M Y');
            } catch (\Throwable $e) {
                return $v;
            }
        })->toArray();
        $counts = $raw->pluck('cnt')->map(fn($v)=>(int)$v)->toArray();
        $sums = $raw->pluck('sum')->map(fn($v)=>(float)$v)->toArray();

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
        $range = $request->get('range', 'lifetime');
        $offset = (int) $request->get('offset', 0);

        $now = Carbon::now();
        if ($offset !== 0) {
            if ($range === 'week') {
                $now->addWeeks($offset);
            } elseif ($range === 'month') {
                $now->addMonths($offset);
            } elseif ($range === '5months') {
                $now->addMonths(5 * $offset);
            }
        }

        // Determine date window
        switch ($range) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $labels = [];
                for ($i=0;$i<7;$i++) { $d = $start->copy()->addDays($i); $labels[] = $d->format('d M'); }
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $days = $now->daysInMonth;
                $labels = [];
                for ($i=0;$i<$days;$i++) { $d = $start->copy()->addDays($i); $labels[] = $d->format('d M'); }
                break;
            case 'lifetime':
                $start = null;
                $end = null;
                $labels = null;
                break;
            case '5months':
            default:
                $start = $now->copy()->startOfMonth()->subMonths(4);
                $end = $now->copy()->endOfMonth();
                $labels = [];
                for ($i = 0; $i < 5; $i++) {
                    $m = $start->copy()->addMonths($i);
                    $labels[] = $m->format('M Y');
                }
                break;
        }

        // Compute Portuguese range label
        $rangeLabel = '';
        if ($range === 'week') {
            $startOfWeek = $now->copy()->startOfWeek();
            $endOfWeek = $now->copy()->endOfWeek();
            $rangeLabel = $startOfWeek->format('d/m') . ' - ' . $endOfWeek->format('d/m/Y');
        } elseif ($range === 'month') {
            $monthsPt = [
                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
            ];
            $rangeLabel = $monthsPt[$now->month] . ' ' . $now->year;
        } elseif ($range === '5months') {
            $startOf5 = $now->copy()->startOfMonth()->subMonths(4);
            $monthsPt = [
                1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
                5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
            ];
            $rangeLabel = $monthsPt[$startOf5->month] . '/' . $startOf5->year . ' - ' . $monthsPt[$now->month] . '/' . $now->year;
        } else {
            $rangeLabel = 'Histórico Completo';
        }

        // Orders by status in window
        $ordersQ = Order::query();
        if ($start) { $ordersQ->where('date', '>=', $start->toDateString()); }
        if ($end) { $ordersQ->where('date', '<=', $end->toDateString()); }
        $ordersByStatus = $ordersQ->selectRaw("status, count(*) as cnt")->groupBy('status')->pluck('cnt','status')->toArray();

        // Monthly (or grouped) counts and sums
        if ($range === 'week' || $range === 'month') {
            // group by day
            $raw = Order::selectRaw("strftime('%Y-%m-%d', date) as ym, count(*) as cnt, sum(total_price) as sum")
                ->where('date', '>=', $start->toDateString())
                ->where('date', '<=', $end->toDateString())
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
            // 5months
            $raw = Order::selectRaw("strftime('%Y-%m', date) as ym, count(*) as cnt, sum(total_price) as sum")
                ->where('date', '>=', $start->toDateString())
                ->where('date', '<=', $end->toDateString())
                ->groupBy('ym')->orderBy('ym')->get()->keyBy('ym');
            $counts = [];$sums = [];
            for ($i = 0; $i < 5; $i++) {
                $m = $start->copy()->addMonths($i);
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
        if ($end) {
            if (!$start) {
                $itemsQ->join('orders','orders.id','=','order_items.order_id');
            }
            $itemsQ->where('orders.date','<=',$end->toDateString());
        }
        $items = $itemsQ->limit(10)->get();
        $topLabels = [];$topValues = [];$topImages = [];
        foreach ($items as $row) {
            $img = TshirtImage::find($row->tshirt_image_id);
            $topLabels[] = $img ? ($img->name ?? 'Design #'.$img->id) : 'Design';
            $topValues[] = (int)$row->total_qty;
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
            'rangeLabel' => $rangeLabel,
        ]);
    }

    /**
     * Export dynamic statistics data to CSV.
     */
    public function export(Request $request)
    {
        $range = $request->get('range', 'lifetime');
        $offset = (int) $request->get('offset', 0);

        $now = Carbon::now();
        if ($offset !== 0) {
            if ($range === 'week') {
                $now->addWeeks($offset);
            } elseif ($range === 'month') {
                $now->addMonths($offset);
            } elseif ($range === '5months') {
                $now->addMonths(5 * $offset);
            }
        }

        // Determine date window
        switch ($range) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'lifetime':
                $start = null;
                $end = null;
                break;
            case '5months':
            default:
                $start = $now->copy()->startOfMonth()->subMonths(4);
                $end = $now->copy()->endOfMonth();
                break;
        }

        // Retrieve orders list in the window
        $q = Order::query()->with(['customer.user']);
        if ($start) { $q->where('date', '>=', $start->toDateString()); }
        if ($end) { $q->where('date', '<=', $end->toDateString()); }
        $orders = $q->orderBy('date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="estatisticas_loja_' . $range . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, ['ID Encomenda', 'Cliente Nome', 'Cliente Email', 'Data', 'Total (€)', 'Estado', 'Tipo Pagamento', 'Referência Pagamento']);

            foreach ($orders as $o) {
                fputcsv($file, [
                    $o->id,
                    $o->customer->user->name ?? 'N/A',
                    $o->customer->user->email ?? 'N/A',
                    $o->date->format('Y-m-d'),
                    $o->total_price,
                    $o->status,
                    $o->payment_type,
                    $o->payment_ref
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}


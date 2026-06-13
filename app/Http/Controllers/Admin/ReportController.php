<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReceiptReport;
use App\Jobs\GenerateReceiptJob;
use App\Jobs\SendReceiptEmailJob;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    protected function ensureAdmin()
    {
        $u = auth()->user();
        if (! $u || ($u->blocked ?? false)) {
            Log::warning('ensureAdmin: user missing or blocked', ['user' => $u ? $u->id : null]);
            abort(403, 'Ação restrita a administradores.');
        }
        $type = strtoupper((string) ($u->user_type ?? ''));
        if (! in_array($type, ['A', 'ADMIN'])) {
            Log::warning('ensureAdmin: not admin', ['user_id' => $u->id ?? null, 'user_type' => $u->user_type ?? null]);
            abort(403, 'Ação restrita a administradores.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        // Retrieve reports, sorting pending ones to the top
        $reports = ReceiptReport::with(['order', 'user'])
            ->orderByRaw("status = 'pending' desc")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function handle(Request $request, ReceiptReport $report)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'action' => 'required|in:accept,reject',
        ]);

        if ($data['action'] === 'accept') {
            $report->status = 'accepted';
            $report->notified = false;

            try {
                // Regenerate receipt PDF and resend e-mail
                GenerateReceiptJob::dispatchSync($report->order_id);
                SendReceiptEmailJob::dispatchSync($report->order_id);
            } catch (\Throwable $e) {
                Log::error('Admin handle report accept: error regenerating receipt for order ' . $report->order_id . ': ' . $e->getMessage());
                return back()->withErrors('Erro ao regenerar ou enviar o recibo.');
            }

            $report->save();
            return back()->with('success', 'O reporte foi aceite. O recibo da encomenda #' . $report->order_id . ' foi regenerado e reenviado.');
        } else {
            $report->status = 'rejected';
            $report->notified = false;
            $report->save();

            return back()->with('success', 'O reporte da encomenda #' . $report->order_id . ' foi recusado.');
        }
    }
}

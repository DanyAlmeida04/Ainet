<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Recibo - Encomenda #{{ $order->id }}</title>
    <style>
        /* Explicit styling for PDF rendering */
        body { font-family: DejaVu Sans, sans-serif; color: #000000; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .items { width: 100%; border-collapse: collapse; }
        .items th, .items td { border: 1px solid #ccc; padding: 8px; color: #000000; }
        .items th { background: #f3f4f6; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FunShirt</h1>
        <div>Recibo - Encomenda #{{ $order->id }}</div>
    </div>

    <div>
        <strong>Cliente:</strong> {{ $order->customer->user->name ?? '---' }}<br />
        <strong>NIF:</strong> {{ $order->nif }}<br />
        <strong>Morada:</strong> {{ $order->address }}<br />
        <strong>Data:</strong> {{ $order->date->format('Y-m-d') }}<br />
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Cor</th>
                <th>Tamanho</th>
                <th>Qtd</th>
                <th class="right">Unit</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $it)
                <tr>
                    <td>{{ $it->tshirtImage->name ?? $it->tshirt_image_id }}</td>
                    <td>{{ $it->color_code }}</td>
                    <td>{{ $it->size }}</td>
                    <td class="right">{{ $it->qty }}</td>
                    <td class="right">€{{ number_format($it->unit_price, 2) }}</td>
                    <td class="right">€{{ number_format($it->sub_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 10px; font-weight: bold;">Total: €{{ number_format($order->total_price, 2) }}</div>
</body>
</html>

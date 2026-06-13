<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FunShirt</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #374151;
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
        }
        .wrapper {
            background-color: #f3f4f6;
            padding: 20px;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #e5e7eb;
        }
        .header {
            background-color: #1e40af;
            padding: 24px;
            text-align: center;
        }
        .logo {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            letter-spacing: 1px;
        }
        .content {
            padding: 32px 24px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #9ca3af;
        }
        h2 {
            margin-top: 0;
            color: #111827;
            font-size: 20px;
            font-weight: 700;
        }
        h3 {
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
            margin-top: 0;
        }
        p {
            margin-top: 0;
            margin-bottom: 16px;
            font-size: 14px;
            color: #4b5563;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            margin: 8px 0;
        }
        .btn-secondary {
            display: inline-block;
            background-color: #f3f4f6;
            color: #4b5563 !important;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            margin: 8px 0;
            border: 1px solid #e5e7eb;
        }
        .card {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: bold;
            background-color: #dbeafe;
            color: #1e40af;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <a href="{{ config('app.url') }}" class="logo">👕 FunShirt</a>
            </div>
            <div class="content">
                @yield('content')
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} FunShirt - Projecto de Aplicações para a Internet.
            </div>
        </div>
    </div>
</body>
</html>

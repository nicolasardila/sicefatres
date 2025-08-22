<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .logo { width: 120px; margin-bottom: 20px; }
        .chart { width: 100%; margin-top: 20px; }
    </style>
</head>
<body>
    {{-- Logo arriba centrado --}}
    <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">

    <h2>Reporte de Actividades</h2>

    @if($filtros)
        <p><strong>Filtros aplicados:</strong> {{ json_encode($filtros) }}</p>
    @endif

    @if($chartImage)
        <img src="{{ $chartImage }}" class="chart" alt="Gráfico">
    @endif
</body>
</html>

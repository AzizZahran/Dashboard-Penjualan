<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjualan</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 p-6">

    <div class="max-w-6xl mx-auto">

        <!-- Title -->
        <a href="{{ route('penjualan.index') }}" 
           class="text-3xl font-bold mb-6 block hover:text-blue-600">
            Dashboard Penjualan
        </a>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold mb-2">Filter Tanggal</h3>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label class="text-sm">Dari</label>
                    <input type="date" name="from" value="{{ $from }}" 
                           class="w-full border p-2 rounded">
                </div>

                <div>
                    <label class="text-sm">Sampai</label>
                    <input type="date" name="to" value="{{ $to }}" 
                           class="w-full border p-2 rounded">
                </div>

                <div class="flex items-end">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                        Filter
                    </button>
                </div>

            </form>
        </div>

        <!-- Import CSV -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold mb-2">Import Data Penjualan (CSV)</h3>

            <form action="{{ route('penjualan.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="flex gap-3 items-center">
                    <input type="file" name="csv_file" accept=".csv" required
                           class="border p-2 rounded w-full">

                    <button class="bg-green-600 text-white px-4 py-2 rounded">
                        Upload
                    </button>
                </div>

                <p class="text-sm text-gray-500 mt-2">Format: nama_produk, tanggal_penjualan, jumlah, harga</p>
            </form>
        </div>

        <!-- Total Penjualan -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold">Total Penjualan</h3>
            <p class="text-3xl font-bold text-blue-600">
                Rp {{ number_format($total, 0, ',', '.') }}
            </p>
        </div>

        <!-- Bar Chart -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Grafik Total Penjualan (Bar Chart)</h2>
            <canvas id="barChart"></canvas>
        </div>

        <!-- Area Chart -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Tren Penjualan (Area Chart)</h2>
            <canvas id="areaChart"></canvas>
        </div>

        <!-- Table -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Daftar Penjualan</h2>

            <table class="w-full border text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 border">Tanggal</th>
                        <th class="p-2 border">Nama Produk</th>
                        <th class="p-2 border">Jumlah</th>
                        <th class="p-2 border">Harga</th>
                        <th class="p-2 border">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualan as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="border p-2">{{ $p->tanggal_penjualan }}</td>
                        <td class="border p-2">{{ $p->nama_produk }}</td>
                        <td class="border p-2">{{ $p->jumlah }}</td>
                        <td class="border p-2">Rp {{ number_format($p->harga,0,',','.') }}</td>
                        <td class="border p-2">
                            Rp {{ number_format($p->jumlah * $p->harga,0,',','.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <script>
        const labels = {!! json_encode($chartLabels) !!};
        const values = {!! json_encode($chartValues) !!};

        /** BAR CHART */
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Penjualan',
                    data: values,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });

        /** AREA CHART */
        new Chart(document.getElementById('areaChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tren Penjualan',
                    data: values,
                    fill: true,
                    tension: 0.4,
                    backgroundColor: 'rgba(75, 192, 192, 0.35)',
                    borderColor: 'rgba(75, 192, 192, 1)'
                }]
            },
            options: { responsive: true }
        });
    </script>

</body>
</html>
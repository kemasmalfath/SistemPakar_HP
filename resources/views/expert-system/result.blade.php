<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Rekomendasi - Sistem Pakar HP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Hasil Rekomendasi HP</h1>
                <a href="{{ route('expert.system') }}" class="text-blue-600 hover:text-blue-800">
                    ← Kembali ke Form
                </a>
            </div>

            @if(isset($result['error']))
                <!-- Error Message -->
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <strong>Error:</strong> {{ $result['error'] }}
                </div>
            @else
                <!-- User Data Summary -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Data Input Pengguna</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Budget:</strong> Rp {{ number_format($userData['budget'], 0, ',', '.') }}</p>
                            <p><strong>Kebutuhan:</strong> {{ $userData['kebutuhan'] }}</p>
                        </div>
                        <div>
                            <p><strong>Layar:</strong> {{ $userData['layar'] }}</p>
                            <p><strong>Chipset:</strong> {{ $userData['chipset'] }}</p>
                            @if($userData['kebutuhan'] === 'Gaming')
                                <p><strong>Durasi Gaming:</strong> {{ $userData['durasi_gaming'] }} jam/hari</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Final Recommendation -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-blue-800 mb-3">Rekomendasi Utama</h2>
                    <p class="text-lg text-blue-700">{{ $result['final_recommendation'] ?? 'Tidak ada rekomendasi' }}</p>
                </div>

                <!-- Rule Results -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Analisis Sistem Pakar</h2>
                    
                    @if(isset($result['rule_results']['tipe_pengguna']))
                        <div class="mb-4 p-4 bg-green-50 rounded-lg">
                            <h3 class="font-semibold text-green-800">Segmentasi Pengguna:</h3>
                            <p class="text-green-700">{{ $result['rule_results']['tipe_pengguna'] }}</p>
                            @if(isset($result['rule_results']['spesifikasi_minimal']))
                                <div class="mt-2">
                                    <h4 class="font-medium text-green-700">Spesifikasi Minimal:</h4>
                                    <ul class="list-disc list-inside text-green-600">
                                        @foreach($result['rule_results']['spesifikasi_minimal'] as $key => $value)
                                            <li>{{ $key }}: {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(isset($result['rule_results']['prioritas']))
                        <div class="mb-4 p-4 bg-yellow-50 rounded-lg">
                            <h3 class="font-semibold text-yellow-800">Prioritas Kebutuhan:</h3>
                            <ul class="list-disc list-inside text-yellow-700">
                                @foreach($result['rule_results']['prioritas'] as $prioritas)
                                    <li>{{ $prioritas }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(isset($result['rule_results']['cooling']))
                        <div class="mb-4 p-4 bg-purple-50 rounded-lg">
                            <h3 class="font-semibold text-purple-800">Sistem Pendingin:</h3>
                            <p class="text-purple-700">{{ $result['rule_results']['cooling'] }}</p>
                        </div>
                    @endif

                    @if(isset($result['rule_results']['trade_off']))
                        <div class="mb-4 p-4 bg-orange-50 rounded-lg">
                            <h3 class="font-semibold text-orange-800">Trade-Off:</h3>
                            <p class="text-orange-700">{{ $result['rule_results']['trade_off'] }}</p>
                        </div>
                    @endif

                    @if(isset($result['rule_results']['rekomendasi']))
                        <div class="mb-4 p-4 bg-red-50 rounded-lg">
                            <h3 class="font-semibold text-red-800">Peringatan:</h3>
                            <p class="text-red-700">{{ $result['rule_results']['rekomendasi'] }}</p>
                            @if(isset($result['rule_results']['alasan']))
                                <p class="text-red-600 text-sm mt-1">{{ $result['rule_results']['alasan'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Detailed Rules Applied -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Rules yang Diterapkan</h2>
                    <div class="space-y-3">
                        @foreach($result['recommendations'] as $index => $rec)
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <p class="font-medium text-gray-700">{{ $rec['rule'] }}</p>
                                <p class="text-sm text-gray-600">
                                    @if(is_array($rec['result']))
                                        {{ implode(', ', array_map(function($k, $v) {
                                            return is_array($v) ? $k . ': ' . implode(', ', $v) : $k . ': ' . $v;
                                        }, array_keys($rec['result']), $rec['result'])) }}
                                    @else
                                        {{ $rec['result'] }}
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Rekomendasi HP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-8">
                Sistem Pakar Rekomendasi HP
            </h1>

            <form action="{{ route('expert.system.process') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Budget -->
                <div>
                    <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">
                        Budget (Rp)
                    </label>
                    <input type="number" 
                           id="budget" 
                           name="budget" 
                           required
                           min="1000000" 
                           max="20000000"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: 3000000">
                    <p class="text-sm text-gray-500 mt-1">
                        Masukkan budget dalam Rupiah (1jt - 20jt)
                    </p>
                </div>

                <!-- Kebutuhan -->
                <div>
                    <label for="kebutuhan" class="block text-sm font-medium text-gray-700 mb-2">
                        Kebutuhan Utama
                    </label>
                    <select id="kebutuhan" 
                            name="kebutuhan" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kebutuhan</option>
                        <option value="Fotografi">Fotografi</option>
                        <option value="Videografi">Videografi</option>
                        <option value="Gaming">Gaming</option>
                        <option value="Produktivitas">Produktivitas</option>
                        <option value="Harian">Penggunaan Harian</option>
                    </select>
                </div>

                <!-- Durasi Gaming (conditional) -->
                <div id="gaming-duration" class="hidden">
                    <label for="durasi_gaming" class="block text-sm font-medium text-gray-700 mb-2">
                        Durasi Gaming per Hari (jam)
                    </label>
                    <input type="number" 
                           id="durasi_gaming" 
                           name="durasi_gaming" 
                           min="0" 
                           max="10"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: 2">
                </div>

                <!-- Tipe Layar -->
                <div>
                    <label for="layar" class="block text-sm font-medium text-gray-700 mb-2">
                        Preferensi Layar
                    </label>
                    <select id="layar" 
                            name="layar"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="IPS">IPS (Standard)</option>
                        <option value="AMOLED">AMOLED (Rekomendasi)</option>
                        <option value="LCD">LCD (Basic)</option>
                    </select>
                </div>

                <!-- Chipset -->
                <div>
                    <label for="chipset" class="block text-sm font-medium text-gray-700 mb-2">
                        Preferensi Chipset
                    </label>
                    <select id="chipset" 
                            name="chipset"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Mid-Range">Mid-Range (Balance)</option>
                        <option value="Flagship">Flagship (High Performance)</option>
                        <option value="Low-End">Low-End (Budget)</option>
                    </select>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        Dapatkan Rekomendasi
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('kebutuhan').addEventListener('change', function() {
            const gamingDuration = document.getElementById('gaming-duration');
            if (this.value === 'Gaming') {
                gamingDuration.classList.remove('hidden');
            } else {
                gamingDuration.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
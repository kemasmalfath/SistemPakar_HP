<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpertSystemController extends Controller
{
    public function index()
    {
        return view('expert-system.form');
    }

    public function processRecommendation(Request $request)
    {
        $validated = $request->validate([
            'budget' => 'required|numeric|min:1000000|max:20000000',
            'kebutuhan' => 'required|in:Fotografi,Videografi,Gaming,Produktivitas,Harian',
            'durasi_gaming' => 'nullable|numeric|min:0',
            'layar' => 'nullable|in:AMOLED,IPS,LCD',
            'chipset' => 'nullable|in:Low-End,Mid-Range,Flagship'
        ]);

        $userData = [
            'budget' => (int)$validated['budget'],
            'kebutuhan' => $validated['kebutuhan'],
            'durasi_gaming' => $validated['durasi_gaming'] ?? 0,
            'layar' => $validated['layar'] ?? 'IPS',
            'chipset' => $validated['chipset'] ?? 'Mid-Range'
        ];

        // Use PHP-based expert system
        $result = $this->runPHPExpertSystem($userData);

        return view('expert-system.result', [
            'userData' => $userData,
            'result' => $result
        ]);
    }

    private function runPHPExpertSystem($userData)
    {
        $recommendations = [];
        $ruleResults = [];

        // RULE 1-3: Segmentasi Budget
        if ($userData['budget'] <= 2000000) {
            $ruleResults['tipe_pengguna'] = "Entry-Level";
            $ruleResults['spesifikasi_minimal'] = [
                'RAM' => '≤ 4GB', 
                'Chipset' => 'Low-End', 
                'Fungsi' => 'dasar saja'
            ];
            $recommendations[] = ['rule' => 'Entry Level', 'result' => $ruleResults];
        } elseif ($userData['budget'] <= 4000000) {
            $ruleResults['tipe_pengguna'] = "Mid-Range";
            $ruleResults['spesifikasi_minimal'] = [
                'RAM' => '6–8GB', 
                'Chipset' => 'Mid-Range', 
                'Baterai' => '≥ 5000 mAh'
            ];
            $recommendations[] = ['rule' => 'Mid Range', 'result' => $ruleResults];
        } else {
            $ruleResults['tipe_pengguna'] = "Flagship";
            $ruleResults['spesifikasi_minimal'] = [
                'RAM' => '12–16GB', 
                'Chipset' => 'Flagship', 
                'Baterai' => '≥ 6000 mAh'
            ];
            $recommendations[] = ['rule' => 'Flagship', 'result' => $ruleResults];
        }

        // RULE 4-10: Kebutuhan Utama
        switch ($userData['kebutuhan']) {
            case 'Fotografi':
                $ruleResults['prioritas'] = ['Sensor besar', 'OIS', 'EIS', 'Lensa berkualitas'];
                $recommendations[] = ['rule' => 'Fotografi', 'result' => ['prioritas' => $ruleResults['prioritas']]];
                break;
                
            case 'Videografi':
                $ruleResults['prioritas'] = ['OIS+EIS', 'Perekaman 4K', 'Chipset kuat', 'Mikrofon berkualitas'];
                $recommendations[] = ['rule' => 'Videografi', 'result' => ['prioritas' => $ruleResults['prioritas']]];
                break;
                
            case 'Gaming':
                if ($userData['durasi_gaming'] >= 2) {
                    $ruleResults['cooling'] = 'Wajib cooling bagus';
                    $recommendations[] = ['rule' => 'Gaming Durasi Tinggi', 'result' => ['cooling' => $ruleResults['cooling']]];
                } else {
                    $ruleResults['cooling'] = 'Cooling standar cukup';
                    $recommendations[] = ['rule' => 'Gaming Durasi Rendah', 'result' => ['cooling' => $ruleResults['cooling']]];
                }
                
                if ($userData['budget'] >= 8000000) {
                    $ruleResults['spesifikasi'] = ['RAM' => '≥ 12GB', 'Chipset' => 'Flagship'];
                    $recommendations[] = ['rule' => 'Gaming Budget Tinggi', 'result' => ['spesifikasi' => $ruleResults['spesifikasi']]];
                }
                break;
                
            case 'Produktivitas':
                $ruleResults['prioritas'] = ['Storage UFS', 'RAM besar', 'Multitasking lancar'];
                $recommendations[] = ['rule' => 'Produktivitas', 'result' => ['prioritas' => $ruleResults['prioritas']]];
                break;
                
            case 'Harian':
                $ruleResults['prioritas'] = ['Baterai besar', 'Chipset efisien'];
                $recommendations[] = ['rule' => 'Harian', 'result' => ['prioritas' => $ruleResults['prioritas']]];
                break;
        }

        // RULE 11-12: Trade-off
        if ($userData['budget'] <= 3000000) {
            if ($userData['kebutuhan'] == 'Gaming') {
                $ruleResults['trade_off'] = 'Mengorbankan kamera + layar demi chipset kuat';
                $recommendations[] = ['rule' => 'Budget Rendah Gaming', 'result' => ['trade_off' => $ruleResults['trade_off']]];
            } elseif ($userData['kebutuhan'] == 'Fotografi') {
                $ruleResults['trade_off'] = 'Meningkatkan kamera meski chipset sedang';
                $recommendations[] = ['rule' => 'Budget Rendah Fotografi', 'result' => ['trade_off' => $ruleResults['trade_off']]];
            }
        }

        // RULE 13-14: Avoid Rules
        if ($userData['budget'] >= 4000000) {
            if ($userData['layar'] != 'AMOLED') {
                $ruleResults['rekomendasi'] = 'Tolak / Hindari';
                $ruleResults['alasan'] = 'Layar bukan AMOLED di budget tinggi';
                $recommendations[] = ['rule' => 'Hindari Non-AMOLED Budget Tinggi', 'result' => $ruleResults];
            }
            
            if ($userData['chipset'] == 'Low-End') {
                $ruleResults['rekomendasi'] = 'Tolak / Hindari';
                $ruleResults['alasan'] = 'Chipset Low-End di budget tinggi';
                $recommendations[] = ['rule' => 'Hindari Chipset Low-End Budget Tinggi', 'result' => $ruleResults];
            }
        }

        // Generate final recommendation
        $finalRecommendation = $this->generateFinalRecommendation($ruleResults, $userData);

        return [
            'user_data' => $userData,
            'rule_results' => $ruleResults,
            'recommendations' => $recommendations,
            'final_recommendation' => $finalRecommendation
        ];
    }

    private function generateFinalRecommendation($results, $userData)
    {
        $budget = $userData['budget'];
        $kebutuhan = $userData['kebutuhan'];
        
        // Base recommendation berdasarkan budget
        if ($budget <= 2000000) {
            $base = "Rekomendasi Entry-Level: Fokus pada value for money, baterai tahan lama";
        } elseif ($budget <= 4000000) {
            $base = "Rekomendasi Mid-Range: Balance antara performa dan fitur";
        } else {
            $base = "Rekomendasi Flagship: Performa maksimal dengan fitur premium";
        }
        
        // Spesifik berdasarkan kebutuhan
        switch ($kebutuhan) {
            case 'Gaming':
                $spec = "Prioritas: Chipset gaming, cooling system, refresh rate tinggi";
                break;
            case 'Fotografi':
                $spec = "Prioritas: Kamera multi-lens, OIS, sensor besar";
                break;
            case 'Videografi':
                $spec = "Prioritas: Stabilisasi video, kualitas audio, storage besar";
                break;
            case 'Produktivitas':
                $spec = "Prioritas: RAM besar, multitasking, layar nyaman";
                break;
            default: // Harian
                $spec = "Prioritas: Baterai besar, daya tahan, performa stabil";
        }
        
        return "{$base}. {$spec}";
    }
}
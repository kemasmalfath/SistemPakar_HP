<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

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

        // Prepare data for Python
        $pythonData = [
            'budget' => (int)$validated['budget'],
            'kebutuhan' => $validated['kebutuhan'],
            'durasi_gaming' => $validated['durasi_gaming'] ?? 0,
            'layar' => $validated['layar'] ?? 'IPS',
            'chipset' => $validated['chipset'] ?? 'Mid-Range'
        ];

        // Execute Python expert system
        $result = $this->runPythonExpertSystem($pythonData);

        return view('expert-system.result', [
            'userData' => $pythonData,
            'result' => $result
        ]);
    }

    private function runPythonExpertSystem($data)
    {
        $pythonScriptPath = base_path('app/Python/expert_system.py');
        $inputData = json_encode($data);

        try {
            $process = Process::run("python3 \"{$pythonScriptPath}\"", function ($type, $output) use ($inputData) {
                fwrite($output, $inputData);
            });

            if ($process->successful()) {
                return json_decode($process->output(), true);
            } else {
                return [
                    'error' => 'Python process failed',
                    'output' => $process->output(),
                    'error_output' => $process->errorOutput()
                ];
            }
        } catch (\Exception $e) {
            return [
                'error' => 'Exception occurred',
                'message' => $e->getMessage()
            ];
        }
    }
}
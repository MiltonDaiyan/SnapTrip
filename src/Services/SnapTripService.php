<?php

namespace DaiyanMozumder\SnapTrip\Services;

use DaiyanMozumder\SnapTrip\Helpers\PythonRunner;

class SnapTripService
{
    public function optimize(
        string $filePath,
        ?int $targetKb = 400,
        ?int $width = null,
        ?int $height = null,
        ?string $outputFolder = null,
        ?string $watermarkLogo = null
    ) {
        $absoluteInput = storage_path('app/public/' . ltrim($filePath, '/'));
        if (!file_exists($absoluteInput)) {
            throw new \Exception("File not found at absolute path: $absoluteInput");
        }

        $outputFolder = $outputFolder ?? config('snaptrip.output_folder', 'optimized');
        $absoluteOutputFolder = storage_path('app/public/' . ltrim($outputFolder, '/'));

        if (!file_exists($absoluteOutputFolder)) {
            mkdir($absoluteOutputFolder, 0755, true);
        }

        $outputFileName = pathinfo($filePath, PATHINFO_FILENAME) . '.webp';
        $absoluteOutput = $absoluteOutputFolder . '/' . $outputFileName;

        $result = PythonRunner::runScript(
            scriptPath: __DIR__ . '/../../python/image_optimizer.py',
            args: [
                'input' => str_replace('\\','/', $absoluteInput),
                'output' => str_replace('\\','/', $absoluteOutput),
                'max_size_kb' => $targetKb,
                'width' => $width,
                'height' => $height,
                'watermark' => $watermarkLogo ? storage_path('app/public/' . ltrim($watermarkLogo, '/')) : 'null'
            ],
            pythonBinary: config('snaptrip.python_path', 'python3')
        );

        return [
            'original' => $filePath,
            'optimized' => "$outputFolder/$outputFileName",
            'result' => trim($result)
        ];
    }
}

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
        // Absolute input file
        $absoluteInput = storage_path('app/public/' . ltrim($filePath, '/'));
        if (!file_exists($absoluteInput)) {
            throw new \Exception("File not found at absolute path: $absoluteInput");
        }

        // Set output folder
        $outputFolder = $outputFolder ?? 'upload/sliders/optimize';
        $absoluteOutputFolder = storage_path('app/public/' . ltrim($outputFolder, '/'));

        // Ensure output folder exists
        if (!file_exists($absoluteOutputFolder)) {
            mkdir($absoluteOutputFolder, 0755, true);
        }

        // Output file absolute path
        $outputFileName = pathinfo($filePath, PATHINFO_FILENAME) . '.webp';
        $absoluteOutput = $absoluteOutputFolder . '/' . $outputFileName;

        // Absolute path for watermark
        $absoluteWatermark = $watermarkLogo ? storage_path('app/public/' . ltrim($watermarkLogo, '/')) : 'null';

        // Run Python script
        $result = PythonRunner::runScript(
            scriptPath: __DIR__ . '/../../python/image_optimizer.py',
            args: [
                'input' => str_replace('\\','/', $absoluteInput),
                'output' => str_replace('\\','/', $absoluteOutput),
                'max_size_kb' => $targetKb,
                'width' => $width ?? 'null',
                'height' => $height ?? 'null',
                'watermark' => $absoluteWatermark
            ],
            pythonBinary: config('snaptrip.python_path', 'python3')
        );

        // Verify file was created
        if (!file_exists($absoluteOutput)) {
            throw new \Exception("Python script did not create output file: $absoluteOutput. Result: $result");
        }

        return [
            'original' => $filePath,
            'optimized' => "$outputFolder/$outputFileName", // Relative path for DB or URL
            'result' => trim($result)
        ];
    }
}

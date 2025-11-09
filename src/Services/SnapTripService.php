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
        if (!file_exists(public_path($filePath))) {
            throw new \Exception("File not found: $filePath");
        }

        $absoluteInput = public_path($filePath);
        $outputFolder = $outputFolder ?? config('snaptrip.output_folder', 'uploads/optimized');
        $absoluteOutputFolder = public_path($outputFolder);
        $pythonPath = config('snaptrip.python_path', 'python3');

        if (!file_exists($absoluteOutputFolder)) {
            mkdir($absoluteOutputFolder, 0755, true);
        }

        $outputFileName = pathinfo($filePath, PATHINFO_FILENAME) . '.webp';
        $absoluteOutput = $absoluteOutputFolder . '/' . $outputFileName;

        $result = PythonRunner::runScript(
            scriptPath: __DIR__ . '/../../python/image_optimizer.py',
            args: [
                'input' => $absoluteInput,
                'output' => $absoluteOutput,
                'max_size_kb' => $targetKb,
                'width' => $width,
                'height' => $height,
                'watermark' => $watermarkLogo ? public_path($watermarkLogo) : 'null'
            ],
            pythonBinary: $pythonPath
        );

        return [
            'original' => $filePath,
            'optimized' => "$outputFolder/$outputFileName",
            'result' => trim($result)
        ];
    }
}

<?php

namespace DaiyanMozumder\SnapTrip\Helpers;

class PythonRunner
{
    public static function runScript(string $scriptPath, array $args, string $pythonBinary = 'python3')
    {
        // Convert nulls to string 'null' for Python script
        $args = array_map(fn($v) => $v ?? 'null', $args);

        // Build command safely
        $cmdParts = [$pythonBinary, escapeshellarg($scriptPath)];
        foreach ($args as $key => $value) {
            $cmdParts[] = "--$key=" . escapeshellarg($value);
        }

        $cmd = implode(' ', $cmdParts);

        // Log command for debugging
        \Log::info("Running Python command: $cmd");

        // Execute command
        $output = shell_exec($cmd);

        // Log output for debugging
        \Log::info("Python output: $output");

        return $output;
    }
}

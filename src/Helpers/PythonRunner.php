<?php

namespace DaiyanMozumder\SnapTrip\Helpers;

class PythonRunner
{
    public static function runScript(string $scriptPath, array $args, string $pythonBinary = 'python3')
    {
        $cmd = escapeshellcmd("$pythonBinary $scriptPath") . ' ' . implode(' ', array_map(
                fn($k, $v) => "--$k=" . escapeshellarg($v),
                array_keys($args),
                $args
            ));

        return shell_exec($cmd);
    }
}

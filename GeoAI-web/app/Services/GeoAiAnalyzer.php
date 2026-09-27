<?php

namespace App\Services;

use RuntimeException;

class GeoAiAnalyzer
{
    public function analyze(string $location, string $businessType, array $spatialFeatures = []): array
    {
        set_time_limit(120); 

        $python = env('GEOAI_PYTHON', PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3');
        $script = base_path('scripts/geoai_analysis.py');
        
        $payloadData = [
            'location' => $location,
            'business_type' => $businessType,
            'offline' => app()->environment('testing'),
        ];
        // 合并可选的空间特征
        $payloadData = array_merge($payloadData, $spatialFeatures);
        
        $payload = json_encode($payloadData, JSON_THROW_ON_ERROR);

        $process = proc_open(
            sprintf('%s %s', escapeshellarg($python), escapeshellarg($script)),
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            base_path(),
        );

        if (! is_resource($process)) {
            throw new RuntimeException('GeoAI Python process could not be started.');
        }

        fwrite($pipes[0], $payload);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0 || ! $output) {
            throw new RuntimeException(trim($error) ?: 'GeoAI analysis failed.');
        }

        $analysis = json_decode($output, true);

        if (! is_array($analysis) || ! isset($analysis['score'], $analysis['coordinates'])) {
            throw new RuntimeException('GeoAI returned an invalid analysis.');
        }

        return $analysis;
    }
}
<?php

namespace App\Web\Components\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class DurationFormatterExtension implements ExtensionInterface
{
    /**
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('formatDuration', [$this, 'formatDuration']);
    }

    /**
     * Formats duration in seconds into format like "3d 14h 21m 18s"
     *
     * @param int $duration
     * @return string
     */
    public function formatDuration(int $duration): string
    {
        $days = intdiv($duration, 24 * 3600);
        $hours = intdiv($duration % (24 * 3600), 3600);
        $minutes = intdiv($duration % 3600, 60);
        $seconds = $duration % 60;
        $parts = [];
        if ($days > 0) {
            $parts[] = "{$days}d";
        }
        if ($hours > 0) {
            $parts[] = "{$hours}h";
        }
        if ($minutes > 0) {
            $parts[] = "{$minutes}m";
        }
        if ($seconds > 0 || count($parts) === 0) {
            $parts[] = "{$seconds}s";
        }
        return implode(' ', $parts);
    }
}

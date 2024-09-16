<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_diff', [$this, 'getTimeDiff']),
        ];
    }

    public function getTimeDiff(\DateTimeInterface $date): string
    {

        // Filter of the difference between the given date and now.
        $now = new \DateTimeImmutable();
        $diff = $date->diff($now);

        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
        }
        if ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
        }
        if ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
        }

        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        }

        return $diff->i . ' min' . ($diff->i > 1 ? 's' : '');
    }
}

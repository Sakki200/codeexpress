<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NumberFormatExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_number', [$this, 'formatNumber']),
        ];
    }

    public function formatNumber(int $number): string
    {
        if ($number < 1000) {
            return (string) $number;
        }

        if ($number < 1000000) {
            return number_format($number / 1000, 1, '.', '') . 'k';
        }

        return number_format($number / 1000000, 1, '.', '') . 'm';
    }
}

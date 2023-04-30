<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter\Provider;


class CurrencyProviderList
{
    /**
     * @return string[]
     */
    public static function getAll(): array
    {
        return [
            \App\Module\CurrencyConverter\Provider\Floatrates::class,
            \App\Module\CurrencyConverter\Provider\Coinpaprika::class
        ];
    }
}
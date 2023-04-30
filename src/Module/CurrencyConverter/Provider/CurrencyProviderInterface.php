<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter\Provider;

interface CurrencyProviderInterface
{
    /**
     * @param string $defaultRate
     * @return array
     */
    public function getData(string $defaultRate): array;
}
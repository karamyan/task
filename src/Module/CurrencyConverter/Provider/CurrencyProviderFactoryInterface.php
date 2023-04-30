<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter\Provider;

interface CurrencyProviderFactoryInterface
{
    public function create(string $namespace): CurrencyProviderInterface;
}
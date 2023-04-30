<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter\Provider;

interface DataWriterInterface
{
    public function save(array $data): bool;
}
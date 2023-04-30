<?php

namespace App\Module\CurrencyConverter\Provider;

abstract class CurrencyProviderAbstract
{
    /**
     * @param array $data
     * @return array
     */
    abstract public function mappingData(array $data): array;
}
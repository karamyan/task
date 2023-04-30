<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter\Provider;

use Symfony\Component\VarExporter\Exception\ClassNotFoundException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyProviderFactory implements CurrencyProviderFactoryInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
    )
    {
    }

    /**
     * @throws ClassNotFoundException
     */
    public function create(string $namespace): CurrencyProviderInterface
    {
        if (!class_exists($namespace)) {
            throw new ClassNotFoundException("Class with namespace: $namespace does not found.");
        }

        return new $namespace($this->client);
    }
}
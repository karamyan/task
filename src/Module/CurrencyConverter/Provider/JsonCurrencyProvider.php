<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter\Provider;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;


class JsonCurrencyProvider implements CurrencyProviderInterface
{
    public function __construct(private string $filePath)
    {
    }

    /**
     * @param string $defaultRate
     * @return array
     */
    public function getData(string $defaultRate = 'usd'): array
    {
        if (!file_exists($this->filePath . '.json')) {
            throw new FileNotFoundException("File with name: $this->filePath.json does not found.");
        }

        $json = file_get_contents($this->filePath . '.json');

        return json_decode($json, true);
    }

    public function setFilePath($path): void
    {
        $this->filePath = $path;
    }
}
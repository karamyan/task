<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter\Provider;


use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;


class JsonDataWriter implements DataWriterInterface
{
    /**
     * @var string
     */
    private string $fileName;

    /**
     * @param string $filePath
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Save data to json file.
     *
     * @param array $data
     * @return bool
     * @throws IOExceptionInterface
     */
    public function save(array $data): bool
    {
        $filesystem = new Filesystem();

        try {
            $filesystem->dumpFile($this->fileName, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        } catch (IOExceptionInterface $exception) {
            throw  new  $exception("An error occurred while creating your directory at " . $exception->getPath());
        }

        return $filesystem->exists($this->fileName);
    }
}
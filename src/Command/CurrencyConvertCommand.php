<?php

declare(strict_types=1);

namespace App\Command;

use App\Module\CurrencyConverter\Provider\JsonDataWriter;
use Throwable;
use App\Module\CurrencyConverter\Provider\CurrencyProviderFactory;
use App\Module\CurrencyConverter\Provider\CurrencyProviderList;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarExporter\Exception\ClassNotFoundException;
use Symfony\Contracts\HttpClient\HttpClientInterface;


#[AsCommand(
    name: 'get-currency',
    description: 'Get currencies and convert to json.',
    aliases: ['get-currency'],
    hidden: false
)]
class CurrencyConvertCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $client,
    )
    {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ClassNotFoundException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currencyCode =  strtolower($_ENV['BASE_CURRENCY']);
        $providerList = CurrencyProviderList::getAll();

        $result = [];
        $providerFactory = new CurrencyProviderFactory($this->client);

        foreach ($providerList as $class) {
            $provider = $providerFactory->create($class);

            try {
                $data = $provider->getData($currencyCode);
            } catch (Throwable $httpException) {
                $output->writeln($httpException->getMessage());
                return Command::INVALID;
            }

            $result = array_merge($result, $provider->mappingData($data));
        }

        $jsonWriter = new JsonDataWriter(fileName: $currencyCode . '.json');

        try {
            $success = $jsonWriter->save($result);
        } catch (Throwable $exception) {
            $output->writeln($exception->getMessage());
            return Command::INVALID;
        }

        if (!$success) {
            $output->writeln('Error saving currency to json.');
            return Command::FAILURE;
        }

        $output->writeln('Currency rates have been saved to ' . $currencyCode . '.json');
        return Command::SUCCESS;
    }
}
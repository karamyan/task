<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter\Provider;

use Generator;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class Coinpaprika extends CurrencyProviderAbstract implements CurrencyProviderInterface
{
    /**
     * @param HttpClientInterface $client
     */
    public function __construct(
        private readonly HttpClientInterface $client,
    )
    {
    }

    /**
     * @var string
     */
    private string $url = 'https://api.coinpaprika.com/v1/exchanges/coinbase/markets?';

    /**
     * @param string $defaultRate
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getData(string $defaultRate = 'USD'): array
    {
        $response = $this->client->request('GET', $this->url . '?quotes=' . strtoupper($defaultRate));

        $content = $response->getContent();

        return json_decode($content, true);
    }

    /**
     * @param array $data
     * @return array
     */
    public function mappingData(array $data): array
    {
        $item = [];
        foreach ($this->getItem($data) as $val) {
            $item[$val['pair']] = [
                'rate' => number_format((1 / $val['quotes']['USD']['price']), 10),
                'code' => $val['pair'],
            ];
        }

        return $item;
    }

    /**
     * @param array $data
     * @return Generator
     */
    public function getItem(array $data): Generator
    {
        foreach ($data as $val) {
            yield $val;
        }
    }
}
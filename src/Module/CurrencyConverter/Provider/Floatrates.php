<?php

namespace App\Module\CurrencyConverter\Provider;

use Generator;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Floatrates  extends CurrencyProviderAbstract implements CurrencyProviderInterface
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
     * Url of the resource.
     *
     * @var string
     */
    private string $url = 'https://www.floatrates.com';

    /**
     * @param string $defaultRate
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getData(string $defaultRate = 'usd'): array
    {
        $response = $this->client->request('GET', $this->url . '/daily/' . strtolower($defaultRate) . '.json');

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
        foreach ($this->getItem($data) as $key => $val) {
            $item[$val['code']] = [
                'rate' => number_format($val['rate'], 10),
                'code' => $val['code'],
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
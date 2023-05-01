<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter;

use App\Module\CurrencyConverter\Exception\UnknownCurrencyException;
use App\Module\CurrencyConverter\Provider\ConvertItem;
use App\Module\CurrencyConverter\Provider\CurrencyProviderInterface;


class CurrencyConverter
{
    /**
     * @param CurrencyProviderInterface $provider
     * @param ConvertItem $convertItem
     */
    public function __construct(private readonly CurrencyProviderInterface $provider, private readonly ConvertItem $convertItem)
    {
    }


    /**
     * @param string $baseCurrency
     * @return array
     * @throws UnknownCurrencyException
     */
    public function convertByBaseCurrency(string $baseCurrency): array
    {
        $rates = $this->provider->getData();

        if ($_ENV['BASE_CURRENCY'] !== $baseCurrency) {
            if (!array_key_exists($baseCurrency, $rates)) {
                throw new UnknownCurrencyException('The currency specified is not recognized or supported by the system. Please enter a valid currency code and try again.');
            }

            $result = $this->convertAll(rates: $rates, baseCurrency: $baseCurrency, count: true);
        } else {
            $result = $this->convertAll(rates: $rates, baseCurrency: $baseCurrency);
        }

        return $result;
    }

    /**
     * @param int|float $amount
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return ConvertItem
     * @throws UnknownCurrencyException
     */
    public function convert(int|float $amount, string $currencyFrom, string $currencyTo): ConvertItem
    {
        $rates = $this->provider->getData();

        return match ($_ENV['BASE_CURRENCY']) {
            $currencyFrom => $this->baseWithFrom(rates: $rates, amount: $amount, currencyTo: $currencyTo),
            $currencyTo => $this->fromWithBase(rates: $rates, amount: $amount, currencyFrom: $currencyFrom),
            default => $this->fromTo(rates: $rates, amount: $amount, currencyFrom: $currencyFrom, currencyTo: $currencyTo),
        };
    }

    /**
     * @param array $rates
     * @param int|float $amount
     * @param string $currencyFrom
     * @return ConvertItem
     */
    private function fromWithBase(array $rates, int|float $amount, string $currencyFrom): ConvertItem
    {
        $rateFrom = floatval($rates[$currencyFrom]['rate']);
        $rateTo = 1 / $rateFrom;
        $convertedAmount = $amount / $rateFrom;

        return $this->generateResponse(convertedAmount: $convertedAmount, rateFrom: $rateFrom, rateTo: $rateTo, changeRate: $rateFrom);
    }

    /**
     * @param array $rates
     * @param int|float $amount
     * @param string $currencyTo
     * @return ConvertItem
     */
    private function baseWithFrom(array $rates, int|float $amount, string $currencyTo): ConvertItem
    {
        $rateTo = floatval($rates[$currencyTo]['rate']);
        $rateFrom = 1 / $rateTo;
        $convertedAmount = $amount * $rateTo;

        return $this->generateResponse(convertedAmount: $convertedAmount, rateFrom: $rateFrom, rateTo: $rateTo, changeRate: $rateFrom);
    }

    /**
     * @param array $rates
     * @param int|float $amount
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return ConvertItem
     * @throws UnknownCurrencyException
     */
    private function fromTo(array $rates, int|float $amount, string $currencyFrom, string $currencyTo): ConvertItem
    {
        if (!array_key_exists($currencyFrom, $rates) || !array_key_exists($currencyTo, $rates)) {
            throw new UnknownCurrencyException('The currency specified is not recognized or supported by the system. Please enter a valid currency code and try again.');
        }

        $rateFrom = floatval($rates[$currencyFrom]['rate']);
        $rateTo = floatval($rates[$currencyTo]['rate']);
        $convertedAmount = ($amount / $rateFrom) * $rateTo;
        $changeRate = $rateFrom / $rateTo;

        return $this->generateResponse(convertedAmount: $convertedAmount, rateFrom: $rateFrom, rateTo: $rateTo, changeRate: $changeRate);
    }

    /**
     * @param float|int $convertedAmount
     * @param float|int $rateFrom
     * @param float|int $rateTo
     * @param float|int $changeRate
     * @return ConvertItem
     */
    private function generateResponse(float|int $convertedAmount, float|int $rateFrom, float|int $rateTo, float|int $changeRate): ConvertItem
    {
        $this->convertItem->setAmount($convertedAmount);
        $this->convertItem->setRateFrom($rateFrom);
        $this->convertItem->setRateTo($rateTo);
        $this->convertItem->setChangeRate($changeRate);

        return $this->convertItem;
    }

    /**
     * @param array $rates
     * @param string $baseCurrency
     * @param bool $count
     * @return array
     */
    private function convertAll(array $rates, string $baseCurrency, bool $count = false): array
    {
        $result = [];
        foreach ($rates as $rate) {
            if ($count) {
                $baseRate = $rates[$baseCurrency];
                if ($rate['code'] == $baseRate['code']) {
                    $newRate = 1 / floatval($baseRate['rate']);
                    $newCurrencyCode = $_ENV['BASE_CURRENCY'];
                } else {
                    $oldRate = floatval($rate['rate']);
                    $newRate = $oldRate / floatval($baseRate['rate']);
                    $newCurrencyCode = $rate['code'];
                }
                $rate['rate'] = $newRate;
                $rate['code'] = $newCurrencyCode;
            }

            $result[] = [
                [
                    "rate" => number_format(floatval($rate['rate']), 10),
                    "code" => $rate['code']
                ],
                [
                    "rate" => 1,
                    "code" => $baseCurrency
                ]
            ];
        }

        return $result;
    }
}
<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter\Provider;

class ConvertItem
{
    private int|float $amount;

    /**
     * @var string
     */
    private string $currencyFrom;

    /**
     * @var int|float
     */
    private int|float $rateFrom;

    /**
     * @var string
     */
    private string $currencyTo;

    /**
     * @var int|float
     */
    private int|float $rateTo;

    /**
     * @var int|float
     */
    private int|float $changeRate;

    /**
     * @return float|int
     */
    public function getAmount(): float|int
    {
        return $this->amount;
    }

    /**
     * @param float|int $amount
     */
    public function setAmount(float|int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return int|float
     */
    public function getRateFrom(): int|float
    {
        return $this->rateFrom;
    }

    /**
     * @return int|float
     */
    public function getRateTo(): int|float
    {
        return $this->rateTo;
    }

    /**
     * @param int|float $rate
     * @return void
     */
    public function setRateFrom(int|float $rate): void
    {
        $this->rateFrom = $rate;
    }

    /**
     * @param int|float $rate
     * @return string
     */
    public function setRateTo(int|float $rate): void
    {
        $this->rateTo = $rate;
    }

    /**
     * @param int|float $rate
     * @return void
     */
    public function setChangeRate(int|float $rate): void
    {
        $this->changeRate = $rate;
    }

    /**
     * @return void
     */
    public function getChangeRate(): int|float
    {
        return $this->changeRate;
    }

    /**
     * @return string
     */
    public function getCurrencyFrom(): string
    {
        return $this->currencyFrom;
    }

    /**
     * @param string $currencyFrom
     */
    public function setCurrencyFrom(string $currencyFrom): void
    {
        $this->currencyFrom = $currencyFrom;
    }

    /**
     * @return string
     */
    public function getCurrencyTo(): string
    {
        return $this->currencyTo;
    }

    /**
     * @param string $currencyTo
     */
    public function setCurrencyTo(string $currencyTo): void
    {
        $this->currencyTo = $currencyTo;
    }
}
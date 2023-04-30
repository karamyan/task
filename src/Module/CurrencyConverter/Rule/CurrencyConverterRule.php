<?php

declare(strict_types=1);

namespace App\Module\CurrencyConverter\Rule;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Positive;


class CurrencyConverterRule
{
    /**
     * @return Collection
     */
    public static function getConvertRule(): Collection
    {
        return new Collection([
            'amount' => [
                new NotNull(message: 'amount field is required'),
                new NotBlank(),
                new Regex(pattern: "/^(?:-(?:[1-9](?:\\d{0,2}(?:,\\d{3})+|\\d*))|(?:0|(?:[1-9](?:\\d{0,2}(?:,\\d{3})+|\\d*))))(?:.\\d+|)$/"),
                new Type(type: 'numeric'),
                new Positive(),
            ],
            'currency_from' => [
                new NotBlank(),
                new NotNull(message: 'currency_from field is required.'),
            ],
            'currency_to' => [
                new NotBlank(),
                new NotNull(message: 'currency_from field is required.'),
            ]
        ]);
    }
}
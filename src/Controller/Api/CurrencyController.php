<?php

namespace App\Controller\Api;

use App\Module\CurrencyConverter\CurrencyConverter;
use App\Module\CurrencyConverter\Exception\UnknownCurrencyException;
use App\Module\CurrencyConverter\Provider\ConvertItem;
use App\Module\CurrencyConverter\Provider\JsonCurrencyProvider;
use App\Module\CurrencyConverter\Rule\CurrencyConverterRule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CurrencyController extends AbstractController
{
    public function __construct(private readonly ParameterBagInterface $params)
    {
    }

    /**
     * @throws UnknownCurrencyException
     */
    #[Route('/get_rates')]
    public function getRates(Request $request): Response
    {
        $baseCurrency = strtoupper($request->query->get('base', $_ENV['BASE_CURRENCY']));

        $filePath = $this->params->get('kernel.project_dir') . '/' . strtolower($_ENV['BASE_CURRENCY']);
        $provider = new JsonCurrencyProvider($filePath);
        $currencyConverter = new CurrencyConverter($provider, new ConvertItem());
        $result = $currencyConverter->convertByBaseCurrency($baseCurrency);

        return new JsonResponse(json_encode($result, JSON_PRETTY_PRINT), 200, [], true);

        $currencyConverter = new CurrencyConverter($provider, ConvertItem());


        if (!isset($currencyRates[$quotedCurrency])) {
            return new JsonResponse(["error" => "Base currency not found"], 400);
        }

        $rate = $currencyRates[$quotedCurrency];
        $result = [
            [
                "rate" => 1,
                "code" => $baseCurrency
            ],
            [
                "rate" => $rate['rate'],
                "code" => $quotedCurrency
            ]
        ];

        return new JsonResponse(json_encode($result, JSON_PRETTY_PRINT), 200, [], true);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     * @throws UnknownCurrencyException
     */
    #[Route('/convert')]
    public function convert(Request $request, ValidatorInterface $validator): Response
    {
        $body = json_decode($request->getContent(), true);
        $rules = CurrencyConverterRule::getConvertRule();

        //TODO change validation part to best practices for symfony.
        $violations = $validator->validate($body, $rules);

        if ($violations->count()) {
            throw new ValidationFailedException('', $violations);
        }

        $amount = $body['amount'];
        $currencyFrom = strtoupper($body['currency_from']);
        $currencyTo = strtoupper($body['currency_to']);

        $filePath = $this->params->get('kernel.project_dir') . '/' . strtolower($_ENV['BASE_CURRENCY']);
        $provider = new JsonCurrencyProvider($filePath);
        $convertItem = new ConvertItem();
        $convertItem->setCurrencyFrom($currencyFrom);
        $convertItem->setCurrencyTo($currencyTo);
        $currencyConverter = new CurrencyConverter($provider, $convertItem);

        $convertItem = $currencyConverter->convert(amount: $amount, currencyFrom: $currencyFrom, currencyTo: $currencyTo);

        $response = [
            'amount' => $convertItem->getAmount(),
            'currency_from' => [
                'rate' => $convertItem->getChangeRate(),
                'code' => $convertItem->getCurrencyFrom()
            ],
            'currency_to' => [
                'rate' => 1,
                'code' => $convertItem->getCurrencyTo()
            ],
        ];

        return new JsonResponse(json_encode($response, JSON_PRETTY_PRINT), 200, [], true);
    }
}
Currency exchange service

Requirements

    Php version >= 8.1

Getting Started

    1. `symfony serve` to run  local server
    2. `bin/console  get-currency` run command to get currencies and save in json file.

Endpoints

    1.  POST `{host}/convert`

        request body.
            `
                {
                    "amount": "11",
                    "currency_from":"EUR",
                    "currency_to":"AMD"
                }
            `

        response body
            `
                {
                    "amount": "4,676.8325884363",
                    "currency_from": {
                        "rate": "0.0023520192",
                        "code": "EUR"
                    },
                    "currency_to": {
                        "rate": 1,
                        "code": "AMD"
                    }
                }
            `
    2. GET `/get_rates?base=USD`

        response body
            `
                [
                    [
                        {
                            "rate": "0.9095264936",
                            "code": "EUR"
                        },
                        {
                            "rate": 1,
                            "code": "USD"
                        }
                    ],
                    [
                        {
                            "rate": "0.8010997974",
                            "code": "GBP"
                        },
                        {
                            "rate": 1,
                            "code": "USD"
                        }
                    ]
                ]
            `
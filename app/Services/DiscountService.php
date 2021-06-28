<?php

namespace App\Services;

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;

final class DiscountService
{
    private Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getDiscountFromService(int $productId): float
    {
        try {
            $discountServerUrl = Env::get('DISCOUNT_SERVER_URL');
            $client = $this->httpClient->request('GET',
                $discountServerUrl . '?id=' . $productId
            );

            if ($client->getStatusCode() != 200) {
                throw new Exception(
                    "Discount service unavailable",
                    $client->getStatusCode()
                );
            }

            $response = $client->getBody();

            return number_format(json_decode($response), 2);

        } catch (Exception $e) {
            Log::critical("Discount service unavailable | " . $e->getMessage());
            return 0;
        }
    }
}

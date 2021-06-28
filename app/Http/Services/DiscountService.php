<?php

namespace App\Http\Services;

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;

final class DiscountService
{
    private Client $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public function getDiscountFromService(int $productId): int
    {
        try {
            $discountServerUrl = Env::get('DISCOUNT_SERVER_URL');
            $client = $this->guzzleClient->request('GET',
                $discountServerUrl . '?id=' . $productId
            );

            if ($client->getStatusCode() != 200) {
                throw new Exception(
                    "Discount service not available",
                    $client->getStatusCode()
                );
            }

                $response = $client->getBody();
            return number_format(json_decode($response), 2);

        } catch (Exception $e) {
            Log::critical("Discount service not available | " . $e->getMessage());
            return 0;
        }
    }
}

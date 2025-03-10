<?php

namespace App\Service;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use DateTimeImmutable;

class MongoDBService {
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient) {
        $this->httpClient = $httpClient;
    }

    public function insertVisit(string $pageName) {
        $this->httpClient->request('POST', 'https://us-east-2.aws.neurelo.com/rest/visits/__one', [
            'headers' => [
                'X-API-KEY' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6ImFybjphd3M6a21zOnVzLWVhc3QtMjowMzczODQxMTc5ODQ6YWxpYXMvYjJjYWNlYWItQXV0aC1LZXkifQ.eyJlbnZpcm9ubWVudF9pZCI6Ijk5NDkzZjhjLTZlZmItNDA2Mi05NjhiLWIyZGU3NTNlZDM0YyIsImdhdGV3YXlfaWQiOiJnd19iMmNhY2VhYi0yYTRlLTQ3YzYtOTlkZS1iNDM3M2I4NWE2MjIiLCJwb2xpY2llcyI6WyJSRUFEIiwiV1JJVEUiLCJVUERBVEUiLCJERUxFVEUiLCJDVVNUT00iXSwiaWF0IjoiMjAyNS0wMy0xMFQxNzo0NTo0Ny41NzMxNjQ1NTVaIiwianRpIjoiMTNjMTc0NTMtYWEwMS00ZTU0LTkyYzMtNzMxZTU3MGRhYmY2In0.fOEPVgBGZoBhu5PJC5prCa8auHc5R6xOCXta0yzMcu_7gW2BRyJXprDJcStO-0hE8ZfCF8N_xIZImAcBfzuvzRqag4_bKtfuIv8xJtObVH-z0qfNqf8JLiDSgk-YXRuGV9oZHuT1TaK2o4C51mXQjJ6sJWuOBOTkUH6KLHwXKXzatmUB4gN17UmRIZ1DMqXtBj4__jY9dliDGOV9wlHiByrbKY3OBW5WAnsBjRxedWc6PS_KpUzHVUOBKR2V8_YrG359wx5NOfoPCz4SrtsOVyM-qn56ank796L9AVFojHAKFvaRM-r-QANsYCToIBm3hIDGgW6N1zFZRUK1r4LeRA',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'pageName' => $pageName,
                'visitedAt' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}

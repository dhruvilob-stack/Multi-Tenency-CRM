<?php

return [
    'api_key' => env('INTEGRATION_API_KEY', ''),

    'free_apis' => [
        'currency_rates' => [
            'base_url' => env('EXCHANGERATE_API_URL', 'https://api.exchangerate.host'),
            'requires_key' => false,
        ],
        'weather_forecast' => [
            'base_url' => env('OPEN_METEO_API_URL', 'https://api.open-meteo.com'),
            'requires_key' => false,
        ],
        'air_quality' => [
            'base_url' => env('OPENAQ_API_URL', 'https://api.openaq.org/v3'),
            'requires_key' => false,
        ],
        'geocoding' => [
            'base_url' => env('NOMINATIM_API_URL', 'https://nominatim.openstreetmap.org'),
            'requires_key' => false,
        ],
        'industry_news' => [
            'base_url' => env('SPACEFLIGHT_NEWS_API_URL', 'https://api.spaceflightnewsapi.net/v4'),
            'requires_key' => false,
        ],
    ],
];

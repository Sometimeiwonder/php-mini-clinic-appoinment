<?php
use App\Support\Env;
return [
    'app' => [
        'name' => Env::get('APP_NAME', 'Event Booking'),
        'env' => Env::get('APP_ENV', 'prod'),
        'debug' => Env::bool('APP_DEBUG', false),
        'company' => Env::get('COMPANY_NAME', 'Events Co'),
        'max_tickets' => Env::int('MAX_TICKETS_PER_BOOKING', 1),
    ],
];
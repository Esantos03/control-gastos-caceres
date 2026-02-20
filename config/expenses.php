<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the default and base currencies for the application.
    |
    */

    'currency' => [
        'default' => env('DEFAULT_CURRENCY_CODE', 'DOP'),
        'base' => env('BASE_CURRENCY_CODE', 'DOP'),
        'foreign' => env('FOREIGN_CURRENCY_CODE', 'USD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Configuration
    |--------------------------------------------------------------------------
    |
    | Configure default pagination limits for various parts of the application.
    |
    */

    'pagination' => [
        'default_per_page' => env('DEFAULT_PER_PAGE', 15),
        'dashboard_recent_expenses' => env('DASHBOARD_RECENT_EXPENSES_LIMIT', 10),
        'dashboard_top_cards' => env('DASHBOARD_TOP_CARDS_LIMIT', 5),
        'widget_top_cards' => env('WIDGET_TOP_CARDS_LIMIT', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache TTL Configuration
    |--------------------------------------------------------------------------
    |
    | Configure cache time-to-live values in seconds.
    |
    */

    'cache' => [
        'exchange_rate_ttl' => env('CACHE_EXCHANGE_RATE_TTL', 3600),
        'currency_ttl' => env('CACHE_CURRENCY_TTL', 86400),
        'budget_ttl' => env('CACHE_BUDGET_TTL', 300),
        'cors_max_age' => env('CACHE_CORS_MAX_AGE', 86400),
    ],

    /*
    |--------------------------------------------------------------------------
    | Budget Alert Thresholds
    |--------------------------------------------------------------------------
    |
    | Configure percentage thresholds for budget alerts.
    |
    */

    'budget_alerts' => [
        'caution' => env('BUDGET_ALERT_CAUTION', 80),
        'warning' => env('BUDGET_ALERT_WARNING', 90),
        'exceeded' => env('BUDGET_ALERT_EXCEEDED', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Limits
    |--------------------------------------------------------------------------
    |
    | Configure validation limits for various fields.
    |
    */

    'validation' => [
        'installments' => [
            'max' => env('MAX_INSTALLMENTS', 60),
            'min' => env('MIN_INSTALLMENTS', 1),
        ],
        'expense_amount' => [
            'max' => env('MAX_EXPENSE_AMOUNT', 999999999),
            'min' => env('MIN_EXPENSE_AMOUNT', 0.01),
        ],
        'description_length' => env('MAX_DESCRIPTION_LENGTH', 255),
        'notes_length' => env('MAX_NOTES_LENGTH', 1000),
        'check_number_length' => env('MAX_CHECK_NUMBER_LENGTH', 50),
        'card_last_digits' => env('MAX_CARD_LAST_DIGITS', 4),
        'billing_day' => [
            'min' => env('MIN_BILLING_DAY', 1),
            'max' => env('MAX_BILLING_DAY', 31),
        ],
        'payment_day' => [
            'min' => env('MIN_PAYMENT_DAY', 1),
            'max' => env('MAX_PAYMENT_DAY', 31),
        ],
        'exchange_rate' => [
            'month' => [
                'min' => env('MIN_EXCHANGE_RATE_MONTH', 1),
                'max' => env('MAX_EXCHANGE_RATE_MONTH', 12),
            ],
            'year' => [
                'min' => env('MIN_EXCHANGE_RATE_YEAR', 2000),
                'max' => env('MAX_EXCHANGE_RATE_YEAR', 2100),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Configuration
    |--------------------------------------------------------------------------
    |
    | Configure widget settings like polling intervals and display limits.
    |
    */

    'widgets' => [
        'polling_interval' => env('WIDGET_POLLING_INTERVAL', '10s'),
        'table_description_limit' => env('WIDGET_TABLE_DESCRIPTION_LIMIT', 30),
        'expense_table_limit' => env('WIDGET_EXPENSE_TABLE_LIMIT', 40),
    ],

    /*
    |--------------------------------------------------------------------------
    | Report Configuration
    |--------------------------------------------------------------------------
    |
    | Configure report settings.
    |
    */

    'reports' => [
        'default_trend_months' => env('DEFAULT_TREND_MONTHS', 6),
    ],

];

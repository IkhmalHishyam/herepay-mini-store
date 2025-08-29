<?php

namespace App\Services;

class Generator
{
    public static function orderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . time();
    }

    public static function invoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . time();
    }

    public static function transactionNumber(): string
    {
        return 'TXN-' . date('Ymd') . '-' . time();
    }
}
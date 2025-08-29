<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case FPX         = 'fpx';
    case CREDIT_CARD = 'credit_card';
}

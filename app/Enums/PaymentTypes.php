<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static cash()
 * @method static static cheque()
 * @method static static online()
 * @method static static none()
 * @method static static paymentDue()
 * @method static static webPayment()
 */

final class PaymentTypes extends Enum
{
    const cash = 1;
    const cheque = 2;
    const online = 3;
    const none = 4;
    const paymentDue = 5;
    const webPayment = 6;
}

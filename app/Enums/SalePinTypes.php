<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static sale()
 * @method static static not_home()
 * @method static static no()
 * @method static static already_bought()
 * @method static static delivery_pending()
 */

final class SalePinTypes extends Enum
{
    const sale = 1;
    const not_home = 2;
    const no = 3;
    const already_bought = 4;
    const delivery_pending = 5;
}

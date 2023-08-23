<?php

namespace App\Enums;


enum OrderStatusEnum : string {

    case PENDING = 'pending';

    case PRECESSING = 'processing';

    case COMPLETED = 'completed';

    case DECLINED = 'declined';
}

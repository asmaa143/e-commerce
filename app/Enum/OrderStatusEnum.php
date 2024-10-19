<?php

namespace App\Enum;

use App\Traits\Enums\EnumConverter;
use Illuminate\Support\Arr;

enum OrderStatusEnum: string
{
    use EnumConverter;

    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case DISPATCH = 'dispatch';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';





}

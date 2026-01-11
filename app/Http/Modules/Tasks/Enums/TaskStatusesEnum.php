<?php

namespace App\Http\Modules\Tasks\Enums;

enum TaskStatusesEnum: int
{
    case NEW = 1;
    case ACCEPTED = 2;
    case ACTIVE = 3;
    case CONDITIONAL_COMPLETED = 4;
    case COMPLETED = 5;
    case POSTPONED = 6;
    case REJECTED = 7;
}

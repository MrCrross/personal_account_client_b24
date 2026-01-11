<?php

namespace App\Http\Modules\Tasks\Enums;

enum TaskProblemTypesEnum: int
{
    case Document = 1;
    case Equipment = 2;
    case OneC = 3;
    case Site = 4;
}

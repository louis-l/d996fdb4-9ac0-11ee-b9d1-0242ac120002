<?php

namespace App\Enums;

enum ReportType: int
{
    case Diagnostic = 1;
    case Progress = 2;
    case Feedback = 3;
}

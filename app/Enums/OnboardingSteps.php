<?php

namespace App\Enums;

enum OnboardingSteps: string
{
    case SETUP_ACCOUNTS = 'setup_accounts';
    case SETUP_DEDUCTIONS = 'setup_deductions';
    case SETUP_APPROVALS = 'setup_approvals';
    case COMPLETED = 'completed';
}

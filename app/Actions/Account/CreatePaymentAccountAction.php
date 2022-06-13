<?php

namespace App\Actions\Account;

use App\DTOs\PaymentAccountDTO;
use App\Models\PaymentAccount;

class CreatePaymentAccountAction
{
    public function handle(PaymentAccountDTO $dto): void
    {
        PaymentAccount::create($dto->extract());
    }
}

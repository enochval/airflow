<?php

namespace App\Actions\Account;

use App\DTOs\BankDTO;
use App\Models\Bank;

class CreateBankAction
{
    public function handle(BankDTO $dto): void
    {
        Bank::firstOrCreate($dto->extract());
    }
}

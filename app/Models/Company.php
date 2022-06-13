<?php

namespace App\Models;

use App\Enums\AccountTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function country(): HasOne
    {
        return $this->hasOne(Country::class, 'id');
    }

    public function virtualAccount(): HasOne
    {
        return $this->hasOne(PaymentAccount::class)
            ->where('account_type', AccountTypesEnum::VIRTUAL_ACCOUNT->value);
    }
}

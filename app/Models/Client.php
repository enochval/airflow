<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function companyIds() : array
    {
        return $this->companies->pluck('id')->toArray();
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deduction_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('bank_id')
                ->constrained('banks')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('deduction_id')
                ->constrained('deductions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('deduction_type');
            $table->longText('description')->nullable();
            $table->string('bank_code');
            $table->string('account_name');
            $table->string('account_no');
            $table->string('account_type');
            $table->boolean('is_active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deduction_accounts');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentFrequenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_frequencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_type_id')
                ->constrained('payment_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('deduction_account_id')
                ->references('id')
                ->on('deduction_accounts')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('frequency');
            $table->boolean('should_pay');
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
        Schema::dropIfExists('payment_frequencies');
    }
}

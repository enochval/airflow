<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('employee_name');
            $table->string('employee_code');
            $table->string('department')->nullable();
            $table->string('job_role')->nullable();
            $table->string('email');
            $table->string('phone_no')->unique();
            $table->foreignId('bank_id')
                ->constrained('banks')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('account_no');
            $table->string('account_name');
            $table->string('tax_id')->nullable();
            $table->string('pension_id')->nullable();
            $table->string('nhf_id')->nullable();
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
        Schema::dropIfExists('employees');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->references('id')
                ->on('clients')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('logo')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone_no');
            $table->integer('no_of_employees')->nullable();
            $table->foreignId('country_id')
                ->nullable()
                ->references('id')
                ->on('countries')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('state_id')
                ->nullable()
                ->references('id')
                ->on('states')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('tax_id')->nullable();
            $table->boolean('is_active')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}

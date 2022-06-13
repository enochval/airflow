<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryIdToPaymentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_types', function (Blueprint $table) {
            if (config('database.default') == 'mysql') {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');

            } else {
                $table->unsignedInteger('company_id')->nullable()->change();
            }

            $table->foreignId('country_id')
                ->after('id')->constrained('countries')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_types', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete();

            if (config('database.default') == 'mysql') {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }
        });
    }
}

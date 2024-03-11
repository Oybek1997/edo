<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('address_type_id');
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('region_id')->nullable();
            $table->unsignedInteger('district_id')->nullable();
            $table->string('street_address', 255)->nullable();
            $table->string('home_address', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('restrict');
            $table->index('address_type_id');
            $table->foreign('address_type_id')->references('id')->on('address_types')->onDelete('restrict');
            $table->index('country_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('restrict');
            $table->index('region_id');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('restrict');
            $table->index('district_id');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('restrict');
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
        Schema::table('employee_addresses', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropIndex(['employee_id']);
            $table->dropForeign(['address_type_id']);
            $table->dropIndex(['address_type_id']);
            $table->dropForeign(['country_id']);
            $table->dropIndex(['country_id']);
            $table->dropForeign(['region_id']);
            $table->dropIndex(['region_id']);
            $table->dropForeign(['district_id']);
            $table->dropIndex(['district_id']);
        });
        Schema::dropIfExists('employee_addresses');
    }
}

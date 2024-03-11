<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeCoefficientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_coefficients', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('coefficient_id');
            $table->integer('percent');
            $table->date('begin_date');
            $table->date('end_date')->nullable();
            $table->string('order_number', 50)->nullable();
            $table->date('order_date');
            $table->string('description', 255)->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
            $table->softDeletes();

            $table->index('coefficient_id');
            $table->foreign('coefficient_id')->references('id')->on('coefficients')->onDelete('restrict');
            $table->index('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_coefficients', function (Blueprint $table) {
        $table->dropForeign(['employee_id']);
        $table->dropIndex(['employee_id']);
        $table->dropForeign(['coefficient_id']);
        $table->dropIndex(['coefficient_id']);
        });
        Schema::dropIfExists('employee_coefficients');
    }
}

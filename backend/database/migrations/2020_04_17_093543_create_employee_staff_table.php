<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_staff', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('tariff_scale_id');
            $table->string('shifr', 6)->default('000000');
            $table->string('contract_number', 25);
            $table->date('contract_date');
            $table->string('enter_order_number', 25);
            $table->date('enter_order_date');
            $table->date('first_work_date');
            $table->string('leave_order_number', 25)->nullable();
            $table->date('leave_order_date')->nullable();
            $table->boolean('is_main_staff')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('restrict');
            $table->index('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('restrict');
            $table->index('tariff_scale_id');
            $table->foreign('tariff_scale_id')->references('id')->on('tariff_scales')->onDelete('restrict');

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
        Schema::table('employee_staffs', function (Blueprint $table) {
            $table->dropForeignKey(['tariff_scale_id']);
            $table->dropIndex(['tariff_scale_id']);
            $table->dropForeignKey(['staff_id']);
            $table->dropIndex(['staff_id']);
            $table->dropForeignKey(['employee_id']);
            $table->dropIndex(['employee_id']);
        });
        Schema::dropIfExists('employee_staffs');
    }
}

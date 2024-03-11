<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeStaffHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_staff_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('employee_staff_id');
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
            $table->index('employee_staff_id');
            $table->index('employee_id');
            $table->index('staff_id');
            $table->index('tariff_scale_id');
            
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
        Schema::dropIfExists('employee_staff_history');
    }
}

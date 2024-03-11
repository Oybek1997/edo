<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('department_id');
            $table->unsignedInteger('position_id');
            $table->unsignedInteger('range_id')->nullable();
            $table->unsignedInteger('personal_type_id')->nullable();
            $table->unsignedInteger('expence_type_id')->nullable();
            $table->double('rate_count', 5, 2)->default(0);
            $table->date('order_date')->nullable();
            $table->string('order_number', 10)->nullable();
            $table->date('begin_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');


            $table->index('range_id');
            $table->index('personal_type_id');
            $table->index('expence_type_id');
            $table->index('position_id');
            $table->index('department_id');
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
        Schema::table('staff_history', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropIndex(['department_id']);
            $table->dropForeign(['position_id']);
            $table->dropIndex(['position_id']);
            $table->dropForeign(['expence_type_id']);
            $table->dropIndex(['expence_type_id']);
            $table->dropForeign(['personal_type_id']);
            $table->dropIndex(['personal_type_id']);
            $table->dropForeign(['range_id']);
            $table->dropIndex(['range_id']);
            $table->dropIndex(['created_by']);
        });
        Schema::dropIfExists('staff_history');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('department_id');
            $table->unsignedInteger('position_id');
            $table->unsignedInteger('range_id')->nullable();
            $table->unsignedInteger('personal_type_id')->nullable();
            $table->unsignedInteger('expence_type_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->double('rate_count', 5, 2)->default(0);
            $table->date('order_date')->nullable();
            $table->string('order_number', 10)->nullable();
            $table->date('begin_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('insruction_file_path', 255)->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('range_id');
            $table->foreign('range_id')->references('id')->on('ranges')->onDelete('cascade');
            $table->index('personal_type_id');
            $table->foreign('personal_type_id')->references('id')->on('personal_types')->onDelete('cascade');
            $table->index('expence_type_id');
            $table->foreign('expence_type_id')->references('id')->on('expence_types')->onDelete('cascade');
            $table->index('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index('position_id');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
            $table->index('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
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
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeignKey(['department_id']);
            $table->dropIndex(['department_id']);
            $table->dropForeignKey(['position_id']);
            $table->dropIndex(['position_id']);
            $table->dropForeignKey(['expence_type_id']);
            $table->dropIndex(['expence_type_id']);
            $table->dropForeignKey(['branch_id']);
            $table->dropIndex(['branch_id']);
            $table->dropForeignKey(['personal_type_id']);
            $table->dropIndex(['personal_type_id']);
            $table->dropForeignKey(['range_id']);
            $table->dropIndex(['range_id']);
        });
        Schema::dropIfExists('staff');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('department_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('department_type_id');
            $table->string('department_code', 10);
            $table->string('name_uz_latin');
            $table->string('name_uz_cyril');
            $table->string('name_ru')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
            $table->index('department_type_id');
            $table->index('parent_id');
            $table->index('company_id');
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('department_history', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropIndex(['department_id']);
            $table->dropForeign(['company_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['company_id']);
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropForeign(['department_type_id']);
            $table->dropIndex(['department_type_id']);
        });
        Schema::dropIfExists('department_history');
    }
}

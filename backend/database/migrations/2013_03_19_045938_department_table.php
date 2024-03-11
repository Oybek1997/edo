<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('departments', function (Blueprint $table) {
		    $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('department_type_id');
            $table->string('department_code', 10);
            $table->string('name_uz_latin');
            $table->string('name_uz_cyril');
            $table->string('name_ru')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('manager_staff_id')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
            $table->softDeletes();
            $table->index('department_type_id');
            $table->index('manager_staff_id');
            $table->foreign('department_type_id')->references('id')->on('department_types')->onDelete('restrict');
            $table->index('parent_id');
            $table->foreign('parent_id')->references('id')->on('departments')->onDelete('restrict');
            $table->index('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id']);
            $table->dropIndex(['created_by']);
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropForeign(['department_type_id']);
            $table->dropIndex(['department_type_id']);
        });
        Schema::dropIfExists('departments');
    }
}

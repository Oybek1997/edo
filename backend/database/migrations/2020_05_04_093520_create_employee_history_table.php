<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('nationality_id');
            $table->string('tabel',4)->unique();
            $table->string('firstname_uz_latin');
            $table->string('lastname_uz_latin');
            $table->string('middlename_uz_latin')->nullable();
            $table->string('firstname_uz_cyril');
            $table->string('lastname_uz_cyril');
            $table->string('middlename_uz_cyril')->nullable();
            $table->date('born_date');
            $table->string('INN',9);
            $table->string('INPS',14);
            $table->string('file_type', 10)->default('jpg');
            $table->string('avatar_name', 255)->nullable();
            $table->string('avatar_path', 255)->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('employee_id');
            $table->index('nationality_id');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_history');
    }
}

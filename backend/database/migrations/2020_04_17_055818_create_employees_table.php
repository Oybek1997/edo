<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
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
            
            $table->index('nationality_id');
            $table->foreign('nationality_id')->references('id')->on('nationalities')->onDelete('restrict');
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
        Schema::table('staffs', function (Blueprint $table) {
            $table->dropForeignKey(['company_id']);
            $table-->dropIndex(['company_id']);
            $table->dropForeignKey(['nationality_id']);
            $table-->dropIndex(['nationality_id']);
        });
        Schema::dropIfExists('employees');
    }
}

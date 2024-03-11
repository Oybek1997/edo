<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentDetailEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_detail_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_detail_id');
            $table->unsignedInteger('employee_id');
            $table->string('description', 255);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
            $table->index('document_detail_id');
            $table->foreign('document_detail_id')->references('id')->on('document_details')->onDelete('restrict');
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
        Schema::dropIfExists('document_detail_employees');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('department_id');
            $table->unsignedInteger('document_type_id');
            $table->unsignedInteger('document_template_id');
            $table->dateTime('document_date');
            $table->string('document_number', 14)->default('0000-AA-000000');

            $table->unsignedInteger('signer_group_id')->nullable();
            $table->unsignedInteger('created_employee_id')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_employee_id');
            $table->index('updated_by');
            $table->timestamps();
            $table->softDeletes();

            $table->index('signer_group_id');
            $table->index('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
            $table->index('document_type_id');
            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}

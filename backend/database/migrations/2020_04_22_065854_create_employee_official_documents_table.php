<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeOfficialDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_official_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('official_document_type_id');
            $table->string('title', 50);
            $table->string('series', 5);
            $table->string('number', 20);
            $table->string('given_organization', 100);
            $table->date('given_date');
            $table->date('due_date');
            $table->string('file_type', 10)->default('jpg');
            $table->string('file_name', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('restrict');
            $table->index('official_document_type_id');
            $table->foreign('official_document_type_id')->references('id')->on('official_document_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_official_documents', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropIndex(['employee_id']);
            $table->dropForeign(['official_document_type_id']);
            $table->dropIndex(['official_document_type_id']);
        });
        Schema::dropIfExists('employee_official_documents');
    }
}

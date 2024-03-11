<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('department_id');
            $table->unsignedInteger('document_type_id');
            $table->boolean('has_employee')->default(true);
            $table->string('name_uz_latin', 255);
            $table->string('name_uz_cyril', 255);
            $table->string('name_ru', 255);
            $table->string('decription_uz_latin', 255);
            $table->string('decription_uz_cyril', 255);
            $table->string('decription_ru', 255);
            $table->unsignedInteger('signer_group_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
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
        Schema::dropIfExists('document_templates');
    }
}

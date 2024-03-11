<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentSignerTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_signer_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_template_id');
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('action_type_id');
            $table->integer('sequence')->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
            
            $table->index('document_template_id');
            $table->foreign('document_template_id')->references('id')->on('document_templates')->onDelete('restrict');
            $table->index('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('restrict');
            $table->index('action_type_id');
            $table->foreign('action_type_id')->references('id')->on('action_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_signer_templates');
    }
}

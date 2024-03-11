<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentDetailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_detail_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_template_id');
            $table->text('content');
            $table->timestamps();
            $table->index('document_template_id');
            $table->foreign('document_template_id')->references('id')->on('document_templates')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_detail_templates');
    }
}

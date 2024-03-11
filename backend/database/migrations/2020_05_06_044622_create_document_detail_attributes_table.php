<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentDetailAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_detail_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_detail_template_id');
            $table->unsignedInteger('data_type_id');
            $table->string('attribute_name_uz_latin', 255);
            $table->string('attribute_name_uz_cyril', 255);
            $table->string('attribute_name_ru', 255);
            $table->string('value_min_lenght', 255)->nullable();
            $table->string('value_max_lenght', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
            $table->softDeletes();

            $table->index('document_detail_template_id');
            $table->foreign('document_detail_template_id')->references('id')->on('document_detail_templates')->onDelete('restrict');
            $table->index('data_type_id');
            $table->foreign('data_type_id')->references('id')->on('data_types')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_detail_attributes');
    }
}

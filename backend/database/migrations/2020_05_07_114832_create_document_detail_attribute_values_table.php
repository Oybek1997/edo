<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentDetailAttributeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_detail_attribute_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_detail_id');
            $table->unsignedInteger('d_d_attribute_id');
            $table->string('attribute_value', 255);
            $table->timestamps();
            $table->index('document_detail_id');
            $table->foreign('document_detail_id')->references('id')->on('document_details')->onDelete('restrict');
            $table->index('d_d_attribute_id');
            $table->foreign('d_d_attribute_id')->references('id')->on('document_detail_attributes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_detail_attribute_values');
    }
}

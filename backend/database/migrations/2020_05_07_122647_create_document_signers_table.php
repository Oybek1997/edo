<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentSignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_signers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_id');
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('action_type_id');
            $table->integer('sequence')->default(1);
            $table->unsignedInteger('signer_user_id');
            $table->string('description', 255);
            $table->timestamps();
            $table->softDeletes();
            $table->index('document_id');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('restrict');
            $table->index('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('restrict');
            $table->index('action_type_id');
            $table->foreign('action_type_id')->references('id')->on('action_types')->onDelete('restrict');
            $table->index('signer_user_id');
            $table->foreign('signer_user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_signers');
    }
}

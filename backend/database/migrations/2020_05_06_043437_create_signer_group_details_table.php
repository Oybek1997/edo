<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignerGroupDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signer_group_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('signer_group_id');
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('action_type_id');
            $table->integer('sequence')->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('staff_id');
            $table->timestamps();
            
            $table->index('signer_group_id');
            $table->foreign('signer_group_id')->references('id')->on('signer_groups')->onDelete('restrict');
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
        Schema::dropIfExists('signer_group_details');
    }
}

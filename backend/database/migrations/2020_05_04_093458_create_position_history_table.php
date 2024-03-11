<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePositionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('position_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('position_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('position_type_id');
            $table->string('code');
            $table->string('name_uz_latin');
            $table->string('name_uz_cyril');
            $table->string('name_ru');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
            $table->softDeletes();
            $table->index('company_id');
            $table->index('position_id');
            $table->index('position_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('position_history');
    }
}

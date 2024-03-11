<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppealContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appeal_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_uz_latin', 255);
            $table->string('name_uz_cyril', 255);
            $table->string('name_ru', 255);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
		    $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appeal_contents');
    }
}

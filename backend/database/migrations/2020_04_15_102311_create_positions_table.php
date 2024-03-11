<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->increments('id');
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
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict');
            $table->index('position_type_id');
            $table->foreign('position_type_id')->references('id')->on('position_types')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropForeignKey(['position_type_id']);
            $table-->dropIndex(['position_type_id']);
            $table->dropForeignKey(['company_id']);
            $table-->dropIndex(['company_id']);
        });
        Schema::dropIfExists('positions');
    }
}

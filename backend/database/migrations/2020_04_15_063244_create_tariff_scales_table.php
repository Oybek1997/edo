<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffScalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariff_scales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category',10);
            $table->double('salary', 15, 2);
            $table->double('hourly_salary', 15, 2)->default(0);
            $table->string('description', 50)->nullable();
            $table->date('order_date')->nullable();
            $table->string('order_number')->nullable();
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
        Schema::dropIfExists('tariff_scales');
    }
}

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateStaffFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('object_type_id');
            $table->unsignedInteger('file_id');
            $table->unsignedInteger('created_by')->nullable();
            $table->index('created_by');
            $table->index('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('restrict');
            $table->index('object_type_id');
            $table->foreign('object_type_id')->references('id')->on('object_types')->onDelete('restrict');
            $table->index('file_id');
            $table->foreign('file_id')->references('id')->on('files')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_files');
    }
}

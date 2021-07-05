<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->string('command')->nullable();
            $table->string('instruction', 1)->nullable();
            $table->unsignedBigInteger('coordinates_id')->nullable();
            $table->string('direction', 1);
            $table->boolean('is_initial')->default(false);
            $table->boolean('success');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->foreign('coordinates_id')->references('id')->on('coordinates');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movements');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('gender', [1,2])->comment('1: MEN, 2: WOMEN');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedSmallInteger('level');
            $table->timestamps();

            $table
                ->foreign('parent_id')
                ->references('id')
                ->on('person')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person');
    }
}

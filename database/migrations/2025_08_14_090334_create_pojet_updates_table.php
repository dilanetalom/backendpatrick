<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pojet_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('projet_id'); 
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
            $table->integer('progress_percentage');
            $table->string('title');
            $table->text('description');
            $table->json('attachments')->nullable();
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
        Schema::dropIfExists('pojet_updates');
    }
};

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
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    
    $table->string('service');
    $table->string('device');

    
    $table->string('name');
    $table->text('description');
    $table->text('objectives')->nullable();
    $table->date('deadline')->nullable();

    $table->decimal('client_price', 10, 2)->nullable();
    $table->decimal('final_price', 10, 2)->nullable();

    $table->enum('status', [
        'pending',
        'negotiation',
        'accepted',
        'contract_signed',
        'payment_pending',
        'in_progress',
        'completed',
        'cancelled'
    ])->default('pending');
    
    $table->integer('progress')->default(0);
    $table->json('specific_data')->nullable(); // Renommé pour plus de clarté
    $table->string('final_link')->nullable();
    
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
        Schema::dropIfExists('projets');
    }
};

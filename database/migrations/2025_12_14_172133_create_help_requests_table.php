<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('help_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentee_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade'); 
            $table->string('module'); 
            $table->text('description'); 
            $table->dateTime('proposed_date'); 
            $table->enum('type', ['En ligne', 'Présentiel']);
            $table->enum('status', ['En attente', 'Acceptée', 'Refusée'])->default('En attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_requests');
    }
};
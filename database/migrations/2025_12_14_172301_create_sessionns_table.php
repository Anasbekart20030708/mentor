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
        Schema::create('sessionns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('help_request_id')->nullable()->constrained('help_requests')->onDelete('cascade');
$table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentee_id')->constrained('users')->onDelete('cascade');
            $table->string('module');
            $table->dateTime('scheduled_at');
            $table->enum('type', ['online', 'in-person']);
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');            $table->text('mentor_notes')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
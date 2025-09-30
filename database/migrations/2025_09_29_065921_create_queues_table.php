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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('patient_name');
            $table->integer('number'); // Daily counter per service
            $table->string('code')->unique(); // GEN-001, PHR-002
            $table->string('counter')->nullable(); // Counter/desk number
            $table->enum('status', ['waiting', 'called', 'done', 'skipped', 'recalled'])->default('waiting');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['service_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};

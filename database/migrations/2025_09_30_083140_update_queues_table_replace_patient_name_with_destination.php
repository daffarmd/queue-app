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
        Schema::table('queues', function (Blueprint $table) {
            // Add destination_id column as nullable first
            $table->unsignedBigInteger('destination_id')->nullable()->after('service_id');
        });

        // Set default destination for existing queues (use first destination)
        $defaultDestination = \App\Models\Destination::first();
        if ($defaultDestination) {
            \Illuminate\Support\Facades\DB::table('queues')
                ->whereNull('destination_id')
                ->update(['destination_id' => $defaultDestination->id]);
        }

        Schema::table('queues', function (Blueprint $table) {
            // Now make destination_id required and add foreign key
            $table->unsignedBigInteger('destination_id')->nullable(false)->change();
            $table->foreign('destination_id')->references('id')->on('destinations')->cascadeOnDelete();

            // Remove patient_name column
            $table->dropColumn('patient_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            // Add back patient_name column
            $table->string('patient_name')->after('service_id');

            // Remove destination_id column
            $table->dropForeign(['destination_id']);
            $table->dropColumn('destination_id');
        });
    }
};

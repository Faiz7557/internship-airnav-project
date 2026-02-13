<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_flight_stats', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->string('branch_code', 10);

            $table->integer('total_dep');
            $table->integer('total_arr');
            $table->integer('total_flights');

            $table->integer('dom_dep')->default(0);
            $table->integer('dom_arr')->default(0);

            $table->integer('int_dep')->default(0);
            $table->integer('int_arr')->default(0);

            $table->integer('training_dep')->default(0);
            $table->integer('training_arr')->default(0);

            $table->time('peak_hour');
            $table->integer('peak_hour_count');
            $table->integer('runway_capacity');

            $table->timestamps();

            $table->unique(['date', 'branch_code']);            
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_flight_stats');
    }
};
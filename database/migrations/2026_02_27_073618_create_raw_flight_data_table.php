<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('raw_flight_datas', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('kode_cabang');
            for ($i = 0; $i < 24; $i++) {
                $hourCol = 'h' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $table->integer($hourCol)->default(0);
            }
            
            $table->timestamps();
            $table->unique(['date', 'kode_cabang']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('raw_flight_datas');
    }
};

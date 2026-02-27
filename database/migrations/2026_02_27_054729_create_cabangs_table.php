<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cabangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cabang')->unique();
            $table->string('nama');
            $table->timestamps();
        });

        DB::table('cabangs')->insert([
            'kode_cabang' => 'WARR',
            'nama' => 'Surabaya',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('cabangs')->insert([
            'kode_cabang' => 'WADY',
            'nama' => 'Banyuwangi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('cabangs')->insert([
            'kode_cabang' => 'WARA',
            'nama' => 'Malang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('cabangs')->insert([
            'kode_cabang' => 'WARC',
            'nama' => 'Blora',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('cabangs')->insert([
            'kode_cabang' => 'WARD',
            'nama' => 'Kediri',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('cabangs')->insert([
            'kode_cabang' => 'WARE',
            'nama' => 'Jember',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('cabangs')->insert([
            'kode_cabang' => 'WART',
            'nama' => 'Sumenep',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('cabangs')->insert([
            'kode_cabang' => 'WARW',
            'nama' => 'Bawean',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('cabangs');
    }
};

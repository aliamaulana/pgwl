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
        Schema::create('table_kec', function (Blueprint $table) {
            $table->serial4('gid'); // Assuming gid is a primary key
            $table->varchar('wadmkc');
            $table->numeric('shape_are'); // Adjust precision and scale as needed
            $table->numeric('shape_len', 15, 2); // Adjust precision and scale as needed
            $table->geometry('geom', 4326); // For storing geometrical data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */


    public function down(): void
    {
        Schema::dropIfExists('table_kec');
    }
};

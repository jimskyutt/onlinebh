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
        Schema::table('boarding_houses', function (Blueprint $table) {
            $table->unsignedInteger('bed_space_per_room')->default(1)->after('bed_space_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boarding_houses', function (Blueprint $table) {
            $table->dropColumn('bed_space_per_room');
        });
    }
};

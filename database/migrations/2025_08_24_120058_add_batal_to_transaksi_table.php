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
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('alasan_batal')->nullable();
            $table->unsignedBigInteger('dibatalkan_oleh')->nullable();
            $table->foreign('dibatalkan_oleh')->references('id')->on('users');
            $table->dateTime('dibatalkan_pada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['dibatalkan_oleh']);
            $table->dropColumn(['alasan_batal', 'dibatalkan_oleh', 'dibatalkan_pada']);
        });
    }
};

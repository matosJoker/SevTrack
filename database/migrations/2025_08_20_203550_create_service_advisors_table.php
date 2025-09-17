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
        Schema::create('service_advisors', function (Blueprint $table) {
            $table->id();
            $table->string('nama_service_advisor');
            $table->string('no_telp')->nullable();
            $table->string('email')->nullable();
            $table->string('alamat')->nullable();
            $table->unsignedBigInteger('id_bengkel')->nullable();
            $table->foreign('id_bengkel')->references('id')->on('bengkel')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_advisors');
    }
};

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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bengkel')->nullable();
            $table->unsignedBigInteger('id_customers')->nullable(); // Fixed typo in column name
            $table->unsignedBigInteger('id_service_advisors')->nullable();
            $table->decimal('total', 10, 2)->default(0.00);
            $table->string('kilometer')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_bengkel')->references('id')->on('bengkel');
            $table->foreign('id_customers')->references('id')->on('customers'); // Fixed typo in table name
            $table->foreign('id_service_advisors')->references('id')->on('service_advisors');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('transaksi');
    }
};

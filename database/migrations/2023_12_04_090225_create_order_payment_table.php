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
        Schema::create('order_payment', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignUuid('order_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('payer_name');
            $table->integer('paid_amount');
            $table->integer('change_amount');
            $table->string('payment_type');
            $table->timestamps();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payment');
    }
};

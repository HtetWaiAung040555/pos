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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no');
            $table->string('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->restrictOnDelete();
            $table->decimal('total_amount',11,2);
            $table->decimal('paid_amount',11,2);
            $table->decimal('due_amount',11,2);
            $table->string('payment_method');
            $table->foreignId('status_id')->constrained('statuses')->restrictOnDelete();
            $table->dateTime('sale_date');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

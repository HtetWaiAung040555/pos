<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Add warehouse_id column
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('location');

            // Foreign key constraint
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['warehouse_id']);

            // Drop the column
            $table->dropColumn('warehouse_id');
        });
    }
};

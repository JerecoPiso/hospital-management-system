<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Master supplies table
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();

            $table->string('name');
            $table->string('unit');                        // pcs, box, roll
            $table->decimal('selling_price', 12, 2)->nullable(); // optional if billing patient
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // Supply stocks table
        Schema::create('supply_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();

            $table->foreignId('supply_id')->constrained('supplies')->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->decimal('purchase_price', 12, 2)->nullable(); // optional
            $table->integer('reorder_level')->default(100);
            $table->string('unit_type')->default('box');          // box, roll, pack, bottle, etc.
            $table->integer('units_per_package')->default(1);    // pieces per box/roll
            $table->date('expiration_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['supply_id', 'expiration_date']);
        });

        // Track supply usage (IN/OUT)
        Schema::create('supply_movements', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();

            $table->foreignId('supply_stock_id')->constrained('supply_stocks')->cascadeOnDelete();
            $table->integer('quantity');
            $table->enum('type', ['IN', 'OUT']);           // stock added or used
            $table->string('used_for')->nullable();       // e.g., procedure, patient, etc.
            $table->softDeletes();
            $table->timestamps();
        });


        // Supply distribution table
        Schema::create('supply_distributions', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('supply_stock_id')->constrained('supply_stocks')->cascadeOnDelete();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete();
            $table->integer('quantity');                   // how many given to this station
            $table->foreignId('distributed_by')->nullable()->constrained('users')->nullOnDelete(); // staff who distributed
            $table->dateTime('distributed_at')->default(now());
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_distributions');
        Schema::dropIfExists('supply_movements');
        Schema::dropIfExists('supply_stocks');
        Schema::dropIfExists('supplies');
    }
};

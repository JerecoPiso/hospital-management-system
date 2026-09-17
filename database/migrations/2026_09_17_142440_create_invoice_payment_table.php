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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->string('invoice_number')->unique(); // e.g., INV-2026-00001
            $table->foreignId('patient_case_id')->constrained()->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->enum('status', ['unpaid', 'partially_paid', 'paid', 'cancelled'])->default('unpaid');
            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            // Polymorphic relation enables charging Lab Requests, Radiology Orders, Fee Schedules, or custom line items
            $table->nullableMorphs('billable'); // billable_type & billable_id
            $table->string('description'); // e.g., "Wound Suturing", "Chest X-Ray", "CBC"
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_number')->unique(); // e.g., OR-2026-0001
            $table->decimal('amount_paid', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'insurance', 'online_banking', 'e_wallet']);
            $table->string('reference_number')->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->timestamp('paid_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};

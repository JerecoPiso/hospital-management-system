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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->string('medical_record_number')->unique(); // patient number
            $table->string('firstname');
            $table->string('lastname');
            $table->string('middlename')->nullable();
            $table->string('suffix', 10)->nullable();
            $table->date('birthdate');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();

            $table->string('civil_status')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('religion')->nullable();
            $table->string('birthplace')->nullable();
            $table->string('occupation')->nullable();
            $table->string('spouse_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};

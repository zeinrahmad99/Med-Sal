<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_type_id')->constrained('categories', 'id')->cascadeOnDelete();
            $table->string('bussiness_name');
            $table->string('contact_number');
            $table->string('bank_name');
            $table->string('iban');
            $table->string('swift_code');
            $table->enum('status', ['pending', 'active', 'blocked'])->default('pending');
            $table->string('document');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};

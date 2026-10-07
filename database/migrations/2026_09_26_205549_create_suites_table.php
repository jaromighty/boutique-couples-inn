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
        Schema::create('suites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('suite_slug')->unique();
            $table->string('theme_slug');

            $table->string('tier');

            $table->decimal('base_price', 8, 2);
            $table->integer('capacity')->default(2);

            $table->json('amenities')->nullable();
            $table->json('images')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suite_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable();

            $table->date('check_in');
            $table->date('check_out');
            $table->integer('nights');
            $table->decimal('total_price', 8, 2);

            $table->string('status');

            $table->timestamp('expires_at')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('suites');
    }
};

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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();

            // Relasi ke user yang membuat pengaduan (customer)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->text('pengaduan');
            $table->string('foto')->nullable();

            // Status pengaduan, default menunggu saat baru dibuat
            $table->enum('status', ['menunggu', 'diproses', 'selesai'])->default('menunggu');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
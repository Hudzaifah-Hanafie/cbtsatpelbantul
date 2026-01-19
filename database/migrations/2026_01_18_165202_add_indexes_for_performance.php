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
        Schema::table('users', function (Blueprint $table) {
            $table->index('name'); // Untuk pencarian nama
            $table->index('role'); // Untuk filter role
            $table->index('created_at'); // Untuk filter tanggal daftar
        });

        Schema::table('user_tests', function (Blueprint $table) {
            $table->index('completed_at'); // Untuk filter tanggal selesai & sorting
            $table->index('score'); // Untuk sorting skor
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->index('title'); // Untuk sorting judul ujian
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['role']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('user_tests', function (Blueprint $table) {
            $table->dropIndex(['completed_at']);
            $table->dropIndex(['score']);
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->dropIndex(['title']);
        });
    }
};
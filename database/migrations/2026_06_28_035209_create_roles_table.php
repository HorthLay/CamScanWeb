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
       Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->timestamps();
    });

    Schema::create('tabs', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('icon')->nullable();
        $table->string('route')->nullable();
        $table->integer('order')->default(0);
        $table->timestamps();
    });

    Schema::create('role_tab', function (Blueprint $table) {
        $table->id();
        $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
        $table->foreignId('tab_id')->constrained('tabs')->cascadeOnDelete();
        $table->unique(['role_id', 'tab_id']);
        $table->timestamps();
    });

    // Add role_id to users AFTER roles table exists
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['role_id']);
        $table->dropColumn('role_id');
    });

    Schema::dropIfExists('role_tab');
    Schema::dropIfExists('tabs');
    Schema::dropIfExists('roles');
    }
};

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
        $prefix = config('database.prefix');
        Schema::create($prefix . 'task_statuses', function (Blueprint $table) {
            $table->tinyInteger('b_status')->unique()->primary();
            $table->string('name');
            $table->string('variant')->comment('Вариант бейджа для фронта');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('database.prefix');
        Schema::dropIfExists($prefix . 'task_statuses');
    }
};

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
        Schema::table($prefix.'task_messages', function (Blueprint $table) {
            // ID файлов Битрикс-Диска, прикреплённых к сообщению. Контент не дублируем у себя —
            // храним только ссылки и резолвим вложения из Битрикса по запросу.
            $table->json('b_disk_file_ids')->nullable()->after('b_comment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('database.prefix');
        Schema::table($prefix.'task_messages', function (Blueprint $table) {
            $table->dropColumn('b_disk_file_ids');
        });
    }
};

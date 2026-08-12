<?php

use App\Models\Tasks\Task;
use App\Models\User;
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
        Schema::create($prefix.'task_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Task::class, 'task_id');
            $table->foreignIdFor(User::class, 'user_id');
            $table->text('text');
            // ID комментария в задаче Битрикса; null, если отправка не удалась
            $table->unsignedBigInteger('b_comment_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('database.prefix');
        Schema::dropIfExists($prefix.'task_messages');
    }
};

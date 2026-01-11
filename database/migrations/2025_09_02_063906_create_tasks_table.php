<?php

use App\Models\Tasks\TaskProblemType;
use App\Models\Tasks\TaskProblemTypeProblem;
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
        Schema::create($prefix . 'tasks', function (Blueprint $table) {
            $table->id();
            $table->string('person');
            $table->string('person_phone');
            $table->integer('b_task_id');
            $table->foreignIdFor(User::class, 'user_creator_id');
            $table->foreignIdFor(TaskProblemType::class, 'problem_type_id');
            $table->foreignIdFor(TaskProblemTypeProblem::class, 'problem_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('database.prefix');
        Schema::dropIfExists($prefix . 'tasks');
    }
};

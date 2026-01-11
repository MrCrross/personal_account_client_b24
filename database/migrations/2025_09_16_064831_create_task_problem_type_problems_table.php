<?php

use App\Models\Tasks\TaskProblemType;
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
        Schema::create($prefix . 'task_problem_type_problems', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TaskProblemType::class,'problem_type_id');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('database.prefix');
        Schema::dropIfExists($prefix . 'task_problem_type_problems');
    }
};

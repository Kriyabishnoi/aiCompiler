<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('executions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_file_id'); // FK to project_files
            $table->text('output')->nullable();
            $table->string('status')->default('pending');
            $table->string('language')->nullable();
            $table->float('execution_time')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();

            $table->foreign('project_file_id')
                  ->references('id')
                  ->on('project_files')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('executions');
    }
};

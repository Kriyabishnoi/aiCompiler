<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('language')->nullable();
            $table->timestamps();
            $table->softDeletes(); // undo option
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};

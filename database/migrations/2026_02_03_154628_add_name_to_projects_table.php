<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {

            if (!Schema::hasColumn('projects', 'name')) {
                $table->string('name')->after('id');
            }

            if (!Schema::hasColumn('projects', 'language')) {
                $table->string('language')->nullable()->after('name');
            }

            if (!Schema::hasColumn('projects', 'deleted_at')) {
                $table->softDeletes(); // undo option
            }
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['name', 'language', 'deleted_at']);
        });
    }
};

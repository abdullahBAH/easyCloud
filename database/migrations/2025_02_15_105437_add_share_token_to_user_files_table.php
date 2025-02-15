<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('user_files', function (Blueprint $table) {
            $table->string('share_token')->nullable()->unique();
        });
    }
};

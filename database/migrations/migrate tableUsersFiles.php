<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('user_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ربط الملف بالمستخدم
            $table->string('file_name'); // اسم الملف
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_files');
    }
};

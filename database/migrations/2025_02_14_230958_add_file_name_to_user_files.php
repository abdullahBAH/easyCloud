<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_files', function (Blueprint $table) {
            $table->string('file_name')->after('user_id'); // إضافة العمود
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('user_files', function (Blueprint $table) {
            $table->dropColumn('file_name');
        });
    }
};

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
        Schema::create('courses_taken', function (Blueprint $table) {
            $table->increments('CourseID');
            $table->string('CourseCode', 64)->nullable(false);
            $table->string('CourseName', 255)->nullable(false);
            $table->string('Prerequisites', 255);
            $table->float('Grade')->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('courses_taken');
    }
};

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
        Schema::create('courses', function(Blueprint $table) {
            $table->increments('CourseID');
            $table->string('CourseCode', 64)->nullable(false);
            $table->string('CourseName', 255)->nullable(false);
            $table->string('CourseOffering', 64)->nullable(false);
            $table->float('CourseWeight')->nullable(false);
            $table->text('CourseDescription')->nullable(false);
            $table->string('CourseFormat',255);
            $table->string('Prerequisites', 255);
            $table->float('PrerequisiteCredits');
            $table->string('Corequisites', 255);
            $table->string('Restrictions', 255);
            $table->string('Equates', 255);
            $table->text('Department');
            $table->string('Location', 64);
        });

        $csvFile = public_path('excel/CourseList.csv');
        $header = null;

        if(($handle = fopen($csvFile, 'r')) !== false) {
            while(($row = fgetcsv($handle)) !== false) {
                if(!$header) {
                    $header = $row;
                } else {
                    $data = [
                        'CourseCode' => $row[0],
                        'CourseName' => $row[1],
                        'CourseOffering' => $row[2],
                        'CourseWeight' => $row[3],
                        'CourseDescription' => $row[4],
                        'CourseFormat' => $row[5],
                        'Prerequisites' => $row[6],
                        'PrerequisiteCredits' => $row[7],
                        'Corequisites' => $row[8],
                        'Restrictions' => $row[9],
                        'Equates' => $row[10],
                        'Department' => $row[11],
                        'Location' => $row[12]
                    ];
                    DB::table('courses')->insertOrIgnore($data);
                }
            }
            fclose($handle);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('courses');
    }
};

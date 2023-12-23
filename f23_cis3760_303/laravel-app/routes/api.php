<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('v1/course/{courseCode}', [CourseController::class, 'getCourse']);
Route::delete('v1/course/{courseCode}', [CourseController::class, 'deleteCourse']);
Route::post('v1/course', [CourseController::class, 'createCourse']);
Route::put('v1/course', [CourseController::class, 'updateCourse']);

Route::get('v1/prereq/{courseCode}', [CourseController::class, 'getPrereqs']);
Route::get('v1/prereq/compiled/{courseCode}', [CourseController::class, 'getCompiledPrereq']);
Route::post('v1/prereq/compiled', [CourseController::class, 'postCompiledPrereq']);
Route::get('v1/prereq/future/none', [CourseController::class, 'getFuturePrereqsNone']);
Route::get('v1/prereq/future/{courseCode}', [CourseController::class, 'getFuturePrereqs']);
Route::post('v1/prereq/future', [CourseController::class, 'postFuturePrereqs']);

Route::get('v1/subject/all', [CourseController::class, 'getSubjectAll']);
Route::get('v1/subject/{subjectCode}', [CourseController::class, 'getSubject']);

Route::get('v1/student', [CourseController::class, 'getCourseTable']);
Route::post('v1/student/{courseCode}', [CourseController::class, 'postCourseTable']);
Route::delete('v1/student/{courseCode}', [CourseController::class, 'deleteCourseTable']);
Route::put('v1/student/{courseCode}/{grade}', [CourseController::class, 'putCourseTable']);

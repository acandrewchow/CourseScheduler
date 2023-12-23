<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\CoursesTaken;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\PrereqRequest;

class CourseController extends Controller
{

    /*
    ======================================================================
    ||                        Course Functions                          ||
    ======================================================================
    */

    private function addAsterisk(string $courseCode) 
    {
        $courseCodeLocal = $courseCode;
        if (!str_contains($courseCodeLocal, '*')) {
            $numPos = strcspn($courseCodeLocal , '0123456789' );
            $courseCodeLocal = substr($courseCodeLocal, 0, $numPos) . '*' . substr($courseCodeLocal, $numPos);
        }
        return $courseCodeLocal;
    }

    public function getCourse(string $courseCode)
    {
        $courseCode = $this->addAsterisk($courseCode);

        if(Courses::where('CourseCode', $courseCode)->exists()) {
            $course = Courses::select('*')
                ->where('CourseCode', $courseCode)
                ->get();
            return response()->json(
                $course,
                200);
        } else {
            return response()->json([
                'message' => 'Course not found.'
            ],  404);
        }
    }

    public function deleteCourse(string $courseCode)
    {
        $courseCode = $this->addAsterisk($courseCode);

        if(Courses::where('CourseCode', $courseCode)->exists()) {
            $course = Courses::where('CourseCode', $courseCode)->first();
            if($course->delete()) {
                return response()->json([
                    'message' => "Record deleted."
                ],  202);
            } else {
                return response()->json([
                    'message' => 'Internal server error.'
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Course not found.'
            ],  404);
        }
    }

    public function createCourse(CourseRequest $request)
    {
        $validated = $request->validated();
        $courseCode = $this->addAsterisk($validated['CourseCode']);
        
        if(!Courses::where('CourseCode', $courseCode)->exists()) {

            $courses = new Courses;

            $courses->CourseCode = $courseCode;
            $courses->CourseName = $validated['CourseName'];
            $courses->CourseOffering = $validated['CourseOffering'];
            $courses->CourseWeight = $validated['CourseWeight'];
            $courses->CourseDescription = $validated['CourseDescription'];
            $courses->CourseFormat = $validated['CourseFormat'];
            $courses->Prerequisites = $validated['Prerequisites'];
            $courses->PrerequisiteCredits = $validated['PrerequisiteCredits'];
            $courses->Corequisites = $validated['Corequisites'];
            $courses->Restrictions = $validated['Restrictions'];
            $courses->Equates = $validated['Equates'];
            $courses->Department = $validated['Department'];
            $courses->Location = $validated['Location'];

            if($courses->save()) {
                return response()->json([
                    'message' => 'Course created.'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Internal server error.'
                ], 500);
            }

        } else {
            return response()->json([
                'message' => 'Conflict.'
            ], 409);
        }
    }

    public function updateCourse(CourseRequest $request)
    {
        $validated = $request->validated();
        $courseCode = $this->addAsterisk($validated['CourseCode']);

        if(Courses::where('CourseCode', $courseCode)->exists()) {

            $course = Courses::where('CourseCode', $courseCode)->first();

            $course->CourseName = $validated['CourseName'];
            $course->CourseOffering = $validated['CourseOffering'];
            $course->CourseWeight = $validated['CourseWeight'];
            $course->CourseDescription = $validated['CourseDescription'];
            $course->CourseFormat = $validated['CourseFormat'];
            $course->Prerequisites = $validated['Prerequisites'];
            $course->PrerequisiteCredits = $validated['PrerequisiteCredits'];
            $course->Corequisites = $validated['Corequisites'];
            $course->Restrictions = $validated['Restrictions'];
            $course->Equates = $validated['Equates'];
            $course->Department = $validated['Department'];
            $course->Location = $validated['Location'];

            if($course->save()) {
                return response()->json([
                    'message' => 'Course updated.'
                ],  202);
            } else {
                return response()->json([
                    'message' => 'Internal server error.'
                ], 500);
            }           

        } else {
            return response()->json([
                'message' => 'Course not found.'
            ],  404);
        }
    }

    /*
    ======================================================================
    ||                      Prerequisites Functions                     ||
    ======================================================================
    */

    public function getPrereqs(string $courseCode)
    {
        $courseCode = $this->addAsterisk($courseCode);

        if(Courses::where('CourseCode', $courseCode)->exists()) {
            $course = Courses::select('Prerequisites')
                ->where('CourseCode', $courseCode)
                ->get();
            return response()->json(
                $course, 
                200);
        } else {
            return response()->json([
                'message' => 'Course not found.'
            ],  404);
        }
    }

    public function getCompiledPrereq(string $courseCode)
    {
        $courseCode = $this->addAsterisk($courseCode);

        if(Courses::where('CourseCode', $courseCode)->exists()) {
            $course = Courses::select('Prerequisites')
                ->where('CourseCode', $courseCode)
                ->get();
            return response()->json(
                Courses::compilePrerequisites($course),
                200);
        } else {
            return response()->json([
                'message' => 'Course not found.'
            ], 404);
        }
    }

    public function postCompiledPrereq(PrereqRequest $request)
    {
        $validated = $request->safe()->only(['*.CourseCode']);
        $validated = $validated['*']['CourseCode'   ];
        $validated = array_map(function($value){return $this->addAsterisk($value);}, $validated);

        if(empty($validated)) {
            return response()->json([
                'message' => 'Bad request.'
            ], 400);
        }

        $result = Courses::where(function($query) use ($validated) {
            for($i=0; $i < count($validated); $i++) {
                $query->orWhere('CourseCode', $validated[$i]);
            }})
        ->exists();

        if($result) {
            $prerequisites = Courses::select('Prerequisites')
                ->where(function($query) use ($validated) {
                    for($i=0; $i < count($validated); $i++) {
                        $query->orWhere('CourseCode', $validated[$i]);
                    }
                })
                ->get();
            
            if(isset($prerequisites)) {
                $compiled = array();

                foreach($prerequisites as $prereq) {
                    array_push($compiled, Courses::compilePrerequisites($prereq));
                }

                return response()->json(
                    $compiled, 
                    200);
            } else {
                return response()->json([
                    'message' => 'Internal server error.'
                ],  500);
            }
        } else {
            return response()->json([
                'message' => 'No prerequisites found.'
            ], 404);
        }
    }

    public function getFuturePrereqs(string $courseCode)
    {
        $courseCode = $this->addAsterisk($courseCode);

        if(Courses::where('Prerequisites', 'like', '%'.$courseCode.'%')->exists()) {
            $courses = Courses::select('*')
                ->where('Prerequisites', 'like', '%'.$courseCode.'%')
                ->get();
            if(!is_null($courses)) {
                return response()->json(
                    $courses, 
                    200);
            } else {
                return response()->json([
                    'message' => 'Internal server error.'
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'No courses found.'
            ],  404);
        }
    }

    public function getFuturePrereqsNone()
    {
        $courses = Courses::select('*')
            ->where('Prerequisites', 'like', '%'.'N/A'.'%')
            ->get();
        if(!is_null($courses)) {
            return response()->json(
                $courses,
                200);
        } else {
            return response()->json([
                'message' => 'Internal server error.'
            ], 500);
        }
    }

    public function postFuturePrereqs(PrereqRequest $request) 
    {
        $validated = $request->safe()->only(['*.CourseCode']);
        $validated = $validated['*']['CourseCode'   ];
        $validated = array_map(function($value){return $this->addAsterisk($value);}, $validated);

        if(empty($validated)) {
            return response()->json([
                'message' => 'Bad request.'
            ], 400);
        }

        $result = Courses::where(function($query) use ($validated) {
            for($i=0; $i < count($validated); $i++) {
                $query->orWhere('Prerequisites', 'like', '%'.$validated[$i].'%');
            }})
        ->exists();

        if($result) {
            $possible_courses = Courses::select('*')
                ->where(function($query) use ($validated) {
                    for($i=0; $i < count($validated); $i++) {
                        $query->orWhere('Prerequisites', 'like', '%'.$validated[$i].'%');
                    }
                })
                ->get();

            if(isset($possible_courses)) {
                $available_courses = array();

                foreach($possible_courses as $course) {
                    $compiled = Courses::compilePrerequisites($course['Prerequisites']);
                    $compiled = Courses::nestCompiled($compiled);
                    $match = Courses::matchPrerequisites($compiled, $validated);

                    if($match && !in_array($course['CourseCode'], $validated)) {
                        array_push($available_courses, $course);
                    }
                }

                return response()->json(
                    $available_courses,
                    200);

            } else {
                return response()->json([
                    'message' => 'Internal server error.'
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'No prerequisites found.'
            ], 404);
        }
    }

    /*
    ======================================================================
    ||                        Subject Functions                         ||
    ======================================================================
    */

    public function getSubjectAll()
    {
        $subjects = Courses::select('CourseCode')
            ->get();
        if(!is_null($subjects)) {
            return response()->json(
                $subjects,
                200);
        } else {
            return response()->json([
                'message' => 'Internal server error.'
            ], 500);
        }
    }

    public function getSubject(string $subjectCode) 
    {
        if(Courses::where('CourseCode', 'like', '%'.$subjectCode.'%')->exists()) {
            $courses = Courses::select('*')
                ->where('CourseCode', 'like', '%'.$subjectCode.'%')
                ->get();
            if(!is_null($courses)) {
                return response()->json(
                    $courses,
                    200);
            } else {
                return response()->json([
                    'message' => 'Internal server error.'
                ], 500);
            }
            
        } else {
            return response()->json([
                'message' => 'Subject code not found'
            ], 404);
        }
    }

    /*
    ======================================================================
    ||                  Student Course_Taken Functions                  ||
    ======================================================================
    */

    public function getCourseTable()
    {
        $courses = CoursesTaken::select('*')
            ->get();
        if(!is_null($courses)) {
            return response()->json(
                $courses,  
                200);
        } else {
            return response()->json([
                'message' => 'Internal server error.'
            ], 500);
        }
    }

    public function deleteCourseTable($courseCode)
    {
        $courseCode = $this->addAsterisk($courseCode);

        if(CoursesTaken::where('CourseCode', $courseCode)->exists()) {
            $course = CoursesTaken::where('CourseCode', $courseCode)->first();
            if($course->delete()) {
                return response()->json([
                    'message' => 'Record deleted.'
                ], 202);
            } else {
                return response()->json([
                    'message' => 'Internal server error.'
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Course not found.'
            ],  404);
        }
    }

    public function postCourseTable($courseCode)
    {
        $courseCode = $this->addAsterisk($courseCode);

        if(Courses::where('CourseCode', $courseCode)->exists()) {
            $course = Courses::select('*')
                ->where('CourseCode', $courseCode)
                ->first();
           

            if(!CoursesTaken::where('CourseCode', $course->CourseCode)->exists()) {
                $course_new = new CoursesTaken;

                $course_new->CourseCode = $course->CourseCode;
                $course_new->CourseName = $course->CourseName;
                $course_new->Prerequisites = $course->Prerequisites;
                $course_new->Grade = 0.0;

                if($course_new->save()) {
                    return response()->json([
                        'message' => "Record created."
                    ],  202);
                } else {
                    return response()->json([
                        'message' => 'Internal server error.'
                    ], 500);
                }   
            }
            else {
                return response()->json([
                    'message' => 'Conflict.'
                ], 409);
            }
            
        } else {
            return response()->json([
                'message' => 'Course not found.'
            ],  404);
        }
    }

    public function putCourseTable($courseCode, $grade)
    {
        $courseCode = $this->addAsterisk($courseCode);

        if(CoursesTaken::where('CourseCode', $courseCode)->exists()) {
            
            if($grade >= 0.0 && $grade <= 100.0) {
                $course = CoursesTaken::where('CourseCode', $courseCode)->first();

                $course->Grade = $grade;

                if($course->save()) {
                    return response()->json([
                        'message' => 'Grade updated.',
                    ], 202);
                } else {
                    return response()->json([
                        'message' => 'Internal server error.'
                    ], 500);
                }

            } else {
                return response()->json([
                    'message' => 'Bad request.'
                ], 400);
            }

        } else {
            return response()->json([
                'message' => 'Course not found.'
            ], 404);
        }
    }
}

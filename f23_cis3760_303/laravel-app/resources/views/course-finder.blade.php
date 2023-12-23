<!DOCTYPE html>
<html lang="en">
<!-- Head Section -->

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Course Finder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="manifest" href="{{asset('manifest.json')}}">
    <script src="https://unpkg.com/swagger-ui-dist@3/swagger-ui-bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Axios -->
    <script src="{{asset('js/course-finder.js')}}"></script>
    <script src="{{asset('js/scripts.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/style.css')}}" />
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3/swagger-ui.css" />
    <link rel="icon" href="{{asset('img/favicon.webp')}}" type="image/webp" />
    <!-- Accordion https://www.w3schools.com/howto/howto_js_accordion.asp-->
</head>

<!-- Header -->
@include('components.navbar')

<body class="bg-gray-100">
    <div class="container mx-auto mt-8 p-2 sm:p-4 bg-white shadow-lg rounded-lg">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">Course Schedule</h1>
        <form id="course-form" class="flex flex-col mb-2">
            <input type="text" id="course-code" placeholder="Enter Course Code (i.e CIS*1300 or CIS1300)" class="w-full p-2 sm:px-4 rounded-lg border border-gray-300 mb-2" />
            <button type="button" id="add-course" class="btn bg-blue-500 text-white p-2 rounded-lg mb-2 transition duration-300 hover:bg-blue-600">
                Add
                Course
            </button>
            <button type="button" id="generate-courses"
                class="btn bg-green-500 text-white p-2 rounded-lg mb-2 transition duration-300 hover:bg-green-600">
                Explore
                Future
                Course
                Options
            </button>
            <button type="submit" id="clear-courses"
                class="btn bg-red-500 text-white p-2 rounded-lg transition duration-300 hover:bg-red-700">
                Clear
                Courses
            </button>
        </form>
        <table id="my-courses" class="w-full text-center mt-4">
            <thead>
                <tr>
                    <th class="w-1/4">Course Code</th>
                    <th class="w-1/4">Course Name</th>
                    <th class="w-1/4">Credits</th>
                    <th class="w-1/4">Remove</th>
                </tr>
            </thead>
            <tbody>
                <!-- Courses are added here -->
            </tbody>
        </table>
    </div>

    <!-- Available Courses -->
    <div class="container mx-auto p-2 sm:p-4 bg-white shadow-lg rounded-lg mt-2 md:mt-4">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-4">Available Courses</h1>
        <table id="available-courses" class="w-full text-center">
            <thead>
                <tr>
                    <th class="w-1/4">Course Code</th>
                    <th class="w-1/4">Course Name</th>
                    <th class="w-1/4">Credits</th>
                    <th class="w-1/4">Add to Schedule</th>
                </tr>
            </thead>
            <tbody>
                <!-- Courses are added here -->
            </tbody>
        </table>
    </div>

    <!-- Courses without Prerequisistes section -->
    <div class="container mx-auto p-2 sm:p-4 bg-white shadow-lg rounded-lg mt-2 md:mt-4">
        <button class="accordion text-2xl sm:text-3xl lg:text-4xl font-bold mb-2 py-1">
            No Prerequisites Courses
        </button>
        <div class="panel">
            <input type="text" id="course-filter" placeholder="Search Courses (i.e CIS)" class="mb-1 sm:mb-2 px-3 py-1 border rounded-lg" />
            <input type="text" id="semester-filter" placeholder="Search Semester" class="mb-1 sm:mb-2 px-3 py-1 border rounded-lg" />
            <div id="no-prereq-courses" class="grid grid-cols-1 sm:grid-cols-2 gap-1 sm:gap-2">
                <!-- No Prerequisite courses listed here -->
            </div>
        </div>
    </div>

    <!-- CourseFinder Information -->
    <div class="container mx-auto p-2 sm:p-4 bg-white shadow-lg rounded-lg mt-10 mb-8">
        <button class="accordion text-2xl sm:text-3xl lg:text-2xl font-bold mb-2 py-1">
            More Information
        </button>
        <div class="panel">
            <h3 class="text-xl font-semibold mb-2 md:mb-4">Purpose</h3>
            <p class="pl-4">
                The purpose of the Course Finder is to search through all the courses at the University of Guelph
                and show you what you can take based on the prerequisistes that you have fulfilled.
            </p>
            <h3 class="text-xl font-semibold mt-2 mb-2 md:mb-4">Usage</h3>
            <ol class="list-inside pl-4" type="1">
                <li>1. Enter all courses that have been completed the course schedule</li>
                <li>2. Once all desired courses are entered click the <em>'Explore Future Course Options'</em> Button</li>
                <li>3. Review the list of available courses based on your previously taken courses</li>
                <li>4. Add any Additional courses you would like from the list or entere more course codes manually</li>
                <li>5. Regenerate courses for an updated list of future available courses</li>
                <li>6. when finished clear courses with the <em>'Clear Courses'</em> button</li>
            </ol>
            <h3 class="text-xl font-semibold mt-2 mb-2 md:mb-4">Additional Features</h3>
            <ul class="list-inside pl-4" style="list-style-type:disc;">
                <li>View and look through a catalog of all courses without prerequisites</li>
                <ul class="list-inside pl-8" style="list-style-type:circle;">
                    <li>Search for specific courses without prerequisites by course code</li>
                    <li>Search for specific courses without prerequisites by semester offering</li>
                </ul>
                <li>Add courses directly to list of taken courses with <em>'add button'</em></li>
            </ul>
        </div>
    </div>

</body>

</html>
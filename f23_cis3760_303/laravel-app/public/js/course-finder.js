const API_ENDPOINT = "https://cis3760f23-11.socs.uoguelph.ca/api/v1/";
$(document).ready(function () {

    let loading_row =
        `<tr id='loader'>
    <td colspan="4" class="px-3 py-2 sm:px-6 sm:py-3 animate-pulse w-full h-6 bg-gray-300 rounded-xl">
    </td>
</tr>`;

    $("#course-code").on("keypress", function (event) {
        var keyPressed = event.keyCode || event.which;
        if (keyPressed === 13) {
            event.preventDefault();

            $("#add-course").click();

            console.log("testtttt");
            return false;
        }
    });

    let courseCounter = 1; // counter for generating unique IDs
    let studentCourses = []; // List of student courses they have taken
    let completedCredits = 0; // Keeps track of the number of credits a student has completed
    let noPreReqCourses = [{}]; // Keeps track of the courses with no prerequisites

    let noPreReqTable = "#no-prereq-courses";
    let studentCoursesTable = "#my-courses";
    let availableCoursesTable = "#available-courses";

    if (completedCredits === 0) {
        $("#credits_completed").text("N/A");
    }

    loadNoPreReqs(); // Loads courses with no prerequisites

    // Retrieve all the noPreReqs
    async function loadNoPreReqs() {
        try {
            const response = await axios.get(
                `${API_ENDPOINT}prereq/future/none`
            );
            if (response.data) {
                noPreReqCourses = response.data;

                $(noPreReqTable).empty(); // Removes the initial empty element

                // Iterates through the courses and creates the cards
                for (let index = 0; index < noPreReqCourses.length; index++) {
                    // Extracted values that are added to each course card
                    const courseCode = noPreReqCourses[index].CourseCode;
                    const courseTitle = noPreReqCourses[index].CourseName;
                    const courseOffering =
                        noPreReqCourses[index].CourseOffering;
                    const addButton =
                        "<button class='add text-blue-700'>Add</button>";

                    courseCard(
                        noPreReqTable,
                        courseCode,
                        courseTitle,
                        courseOffering,
                        addButton
                    );
                }
            }
        } catch (error) {
            // Handle errors
            console.error(error);
        }
    }

    // Creates a course card to display a given course
    function courseCard(
        tableID,
        courseCode,
        courseTitle,
        courseOffering,
        addButton
    ) {
        const $courseCard = $(
            "<div class='bg-blue-200 p-4 rounded-lg course'></div>"
        );
        $courseCard.append(
            $("<p class='text-xl font-semibold'></p>").text(courseCode)
        );
        $courseCard.append($("<p></p>").text(courseTitle));
        $(tableID).append($courseCard);

        $courseCard.append($("<p></p>").text(courseOffering));
        $(tableID).append($courseCard);

        $courseCard.append($("<p class='mt-4'></p>").html(addButton));
        $(tableID).append($courseCard);
    }

    // Adds row to given table
    function courseRow(tableID, courseData, rowFunction, rowIndex = 1) {
        const table = $(tableID + " tbody");
        const newRow = $("<tr>");
        const uniqueID = `course-${rowIndex++}`;
        newRow.attr("data-course-id", uniqueID);

        $('#loader').remove()
        // Handles duplicate courses
        newRow.append(
            // Course Code
            $("<td>")
                .addClass(
                    "px-3 py-2 sm:px-6 sm:py-3 text-gray-600 text-center border-b-2"
                )
                .text(courseData.CourseCode)
        );
        newRow.append(
            // Course Name
            $("<td>")
                .addClass(
                    "px-3 py-2 sm:px-6 sm:py-3 text-gray-600 text-center border-b-2"
                )
                .text(courseData.CourseName)
        );
        newRow.append(
            // Course Weight
            $("<td>")
                .addClass(
                    "px-3 py-2 sm:px-6 sm:py-3 text-gray-600 text-center border-b-2"
                )
                .text(courseData.CourseWeight)
        );
        newRow.append(
            // Delete button
            $("<td>")
                .addClass("px-3 py-2 sm:px-6 sm:py-3 text-center border-b-2")
                .html(rowFunction)
        );
        table.append(newRow);
    }

    // Adds course that user took
    async function addCourseToTable(courseCode) {
        try {
            // API Call to fetch course information from our API
            const response = await axios.get(
                `${API_ENDPOINT}course/${courseCode}`
            );

            if (response.data) {
                // console.log(response.data);
                const courseData = response.data[0];
                if (!studentCourses.includes(courseData.CourseCode)) {
                    studentCourses.push(courseData.CourseCode); // Keeps track of the courses that are added to the student's schedule
                    deleteButton =
                        "<button class='delete text-red-600'>Delete</button>";
                    courseRow(
                        studentCoursesTable,
                        courseData,
                        deleteButton,
                        courseCounter
                    );
                    completedCredits += courseData.CourseWeight; // Credit tracker
                    $("#credits_completed").text(completedCredits);
                } else {
                    $('#loader').remove()
                    alert("Course already added!");
                }
            }
        } catch (error) {
            // Handle errors
            $('#loader').remove()
            alert("Warning:\nCourse not Found in Database");
            console.error(error);
        }
    }

    // Adds a course to the table
    $("#add-course").click(function () {
        courseCode = $("#course-code").val();
        $(studentCoursesTable).append(loading_row);
        if (courseCode) {
            courseCode = courseCode.split(",");
            setTimeout(
                () => {
                    courseCode.forEach((course) => addCourseToTable(course.trim()));
                }, 500
            )
            $("#course-code").val("");
        } else {
            alert("No course code entered!");
            $("#loader").remove()
        }
    });

    // Generates the courses a student can take
    $("#generate-courses").click(async function () {
        if (studentCourses.length === 0) {
            alert("No courses entered!");
        }
        $(availableCoursesTable + " tbody").empty(); // Removes the existing courses
        $(availableCoursesTable).append(loading_row);
        availableCourses = [];
        // Include logic here to output prerequisites when the button is clicked
        // will require an API call passing in the studentCourses array

        const response = await axios.post(
            `${API_ENDPOINT}prereq/future`,
            (data = studentCourses.map((course) => ({ CourseCode: course })))
        );
        availableCourses = response.data;

        console.log(response.data);

        // Iterates through the courses and creates the cards
        addButton = "<button class='add text-blue-600'>Add</button>";
        availableCourses.forEach(function (course) {
            if (!studentCourses.includes(course.CourseCode)) {
                setTimeout(
                    () => {
                        courseRow(availableCoursesTable, course, addButton);
                    }, 500
                )
            }
        });
    });

    // Removes a course from the table
    $("#my-courses").on("click", ".delete", function () {
        const courseCode = $(this).closest("tr").find("td:first").text();
        const courseWeight = $(this).closest("tr").find("td:eq(2)").text(); // retrieves the 3rd row

        $(this).closest("tr").remove();

        // Find the corresponding course index
        const index = studentCourses.indexOf(courseCode);

        if (index !== -1) {
            // Remove the course from the student's schedule
            studentCourses.splice(index, 1);
            // Update the credit count after removing
            completedCredits -= parseFloat(courseWeight);
            $("#credits_completed").text(completedCredits);
        }
    });

    // Adds a course to user schedule
    $(availableCoursesTable).on("click", ".add", function () {
        tableRow = $(this).closest("tr");
        const courseCode = tableRow.find("td:first").text();
        addCourseToTable(courseCode);
        tableRow.empty();
    });

    $(noPreReqTable).on("click", ".add", function () {
        tableRow = $(this).closest("div");
        const courseCode = tableRow.find("p:first").text();
        addCourseToTable(courseCode);
        tableRow.remove();
    });

    var accordion = $(".accordion");

    accordion.click(function () {
        $(this).toggleClass("active");
        var panel = $(this).next();

        if (panel.css("max-height") === "0px") {
            panel.css("max-height", panel.prop("scrollHeight") + "100px");
            $("#course-filter").css("display", "block");
        } else {
            panel.css("max-height", "0");
            $("#course-filter").css("display", "none");
        }
    });

    // Filters courses based on the user input
    function filterCourses(courseInput, semesterInput) {
        $(noPreReqTable).empty();
        const addButton = "<button class='add text-blue-700'>Add</button>";
        noPreReqCourses.forEach(function (course) {
            if (
                course.CourseCode.toLowerCase().includes(
                    courseInput.toLowerCase()
                ) &&
                course.CourseOffering.toLowerCase().includes(
                    semesterInput.toLowerCase()
                )
            ) {
                courseCard(
                    noPreReqTable,
                    course.CourseCode,
                    course.CourseName,
                    course.CourseOffering,
                    addButton
                );
            }
        });
    }

    // Course filters
    $("#course-filter").on("input", function () {
        var courseFilterValue = $(this).val();
        var semesterFilterValue = $("#semester-filter").val();
        filterCourses(courseFilterValue, semesterFilterValue);
    });

    // Semester Filters
    $("#semester-filter").on("input", function () {
        var courseFilterValue = $("#course-filter").val();
        var semesterFilterValue = $(this).val();
        filterCourses(courseFilterValue, semesterFilterValue);
    });
});

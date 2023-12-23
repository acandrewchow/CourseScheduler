/*==============================================================================
                            Course Roadmap Section
==============================================================================*/
const API_ENDPOINT = "https://cis3760f23-11.socs.uoguelph.ca/api/v1/";

$(document).ready(function () {
    // Allows user to hit enter to submit a subject
    $("#subjectText").on("keypress", function (event) {
        var keyPressed = event.keyCode || event.which;
        if (keyPressed === 13) {
            event.preventDefault();
            $("#generateRoadmapBtn").click();

            return false;
        }
    });

    var accordion = $(".accordion");
    var credit = 0;
    const creditList = [];

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

    // Tooltip showing information when a user hovers over the div
    $(".tooltip").hover(
        function () {
            $(this).find(".tooltip-content").removeClass("hidden");
        },
        function () {
            $(this).find(".tooltip-content").addClass("hidden");
        }
    );
    // function to generate a node
    function GenerateNode(course_code, course_node_group = "") {
        return {
            id: course_code,
            label: course_code,
            group: course_node_group,
        };
    }
    const $toggleIcon = $("#toggleIcon");
    let isToggled = false;

    // Tree Options
    let network_options = {
        physics: {
            enabled: true,
            repulsion: {
                springLength: 10,
                nodeDistance: 20,
                centralGravity: 0,
            },
            maxVelocity: 10,
        },
        interaction: {
            hover: true,
            hoverConnectedEdges: true,
        },
        layout: {
            hierarchical: {
                nodeSpacing: 100,
                treeSpacing: 30,
                direction: "UD",
                sortMethod: "directed",
                shakeTowards: "roots",
            },
        },
        nodes: {
            shape: "circle",
            borderWidth: 0,
            font: {
                color: "#FFFFFF",
            },
            color: {
                border: "#000000",
                background: "#290f28",
                hover: {
                    border: "#6b2769",
                    background: "#6b2769",
                },
                highlight: {
                    border: "#610218",
                    background: "#C20430",
                },
            },
        },
        edges: {
            hidden: isToggled ? false : true,
            color: "#FFC72A",
        },
    };

    let network = null;
    let data = null;
    let container = document.getElementById("subject-roadmap");
    let detailsContainer = document.getElementById("course-details");
    let listContainer = document.getElementById("courses-list");


    // Function to toggle the arrows on/off
    $("#toggleArrowsBtn").on("click", function () {
        isToggled = !isToggled;
        $toggleIcon.attr(
            "class",
            isToggled
                ? "fas fa-toggle-on text-green-500 text-4xl"
                : "fas fa-toggle-off text-gray-500 text-4xl"
        );
        network_options.edges.hidden = isToggled ? false : true;
        if (network !== null) {
            network.setOptions(network_options);
        }
    });

    // Function to reset the roadmap
    $("#resetRoadmapBtn").click(async function () {
        if (network !== null) {
            // reset tree
            $(container).empty();
            network = new vis.Network(container, data, network_options);
            setNetworkEvents();

            // reset course info container
            let displayedInfo = document.createElement("p");
            let newText = document.createTextNode(
                "Course information will appear here once a node has been selected"
            );
            displayedInfo.classList.add("text-gray-400");
            displayedInfo.appendChild(newText);
            detailsContainer.innerHTML = "";
            detailsContainer.appendChild(displayedInfo);
        } else {
            alert("No network to reset!");
        }
    });

    $("#clearRoadmapBtn").click(async function () {
        if (network !== null) {
            // reset tree
            $(container).empty();

            // network = new vis.Network(container, data, network_options);
            // setNetworkEvents()

            // reset course info container
            let displayedInfo = document.createElement("p");
            let newText = document.createTextNode(
                "Course information will appear here once a node has been selected"
            );
            displayedInfo.classList.add("text-gray-400");
            displayedInfo.appendChild(newText);
            detailsContainer.innerHTML = "";
            detailsContainer.appendChild(displayedInfo);

            var counterElement = document.getElementById('counter');
            credit = 0;
            counterElement.textContent  = credit;
        } else {
            alert("No network to reset!");
        }
    });

    // Function to generate the roadmap will require API Calls
    $("#generateRoadmapBtn").click(async function () {
        // 1. User enters subject code (I.E CIS)
        var subject = $("#subjectText").val();
        let loader = $("#loader");
        loader.removeClass('hidden');

        $("#subjectText").val("");

        // 2. go to the subject end point to retrieve all course codes for the particular subject (i.e CIS 1300)
        let subjectCourses = [];
        let compiledPrereqs = [];

        try {
            // Retrieve all subject courses
            const response = await axios.get(
                `${API_ENDPOINT}subject/${subject}`
            );
            if (response.data) {
                subjectCourses = response.data;
            }
        } catch (error) {
            alert(`Failed to retrieve ${subject} courses`);
            loader.addClass('hidden');
            console.log(error);
            return;
        }

        try {
            const response = await axios.post(
                `${API_ENDPOINT}prereq/compiled`,
                (data = subjectCourses.map((course) => ({
                    CourseCode: course["CourseCode"],
                })))
            );
            if (response.data) {
                compiledPrereqs = response.data;
            }
        } catch (error) {
            alert(`Failed to compile ${subject} courses`);
            loader.addClass('hidden');
            console.log(error);
            return;
        }
        // 3. pass in the course code to the get course end point to retrieve pre-requisite data
        // Parse the prerequisite data for the course - each course in prerequisites will represent a FROM course node to the course that is initially
        // passed in the course end point
        // 4. Create nodes for each course as well as the edges which represent the prerequisite to and from a particular course
        let course_nodes = [];
        let course_edges = [];
        let color_dict = {
            and: "#e61919BF",
            or: "#1953e6BF",
            "x of": "##77ff00BF",
        };

        for (let i = 0; i < subjectCourses.length; i++) {
            if (
                !course_nodes.some(
                    (element) =>
                        element["id"] === subjectCourses[i]["CourseCode"]
                )
            )
                course_nodes.push(
                    GenerateNode(subjectCourses[i]["CourseCode"])
                );

            // set default pre-req type to "and"
            compiled = compiledPrereqs[i];
            curr_prereq_type = "and";

            // initial scan for pre-req type
            //  if there are no brackets to parse, then all pre-reqs for the course will use this type
            for (let j = 0; j < compiled.length; j++) {
                if (compiled[j]["type"] === "or") {
                    curr_prereq_type = "or";
                } else if (compiled[j]["type"] === "x of") {
                    curr_prereq_type = "x of";
                }
            }

            // create edges for each pre-req
            for (let j = 0; j < compiled.length; j++) {
                // find type of pre-req inside bracket
                if (compiled[j]["type"] === "open_bracket") {
                    let k = j;
                    curr_prereq_type = "and";
                    while (
                        k < compiled.length &&
                        compiled[k]["type"] != "close_bracket"
                    ) {
                        if (compiled[k]["type"] === "x of") {
                            curr_prereq_type = "x of";
                            break;
                        } else if (compiled[k]["type"] === "or") {
                            curr_prereq_type = "or";
                            break;
                        }
                        k++;
                    }
                }

                // create course node
                if (compiled[j]["type"] === "code") {
                    if (
                        !course_nodes.some(
                            (element) =>
                                element["id"] ===
                                subjectCourses[i]["CourseCode"]
                        )
                    )
                        course_nodes.push(
                            GenerateNode(subjectCourses[i]["CourseCode"])
                        );

                    course_edges.push({
                        from: compiled[j]["data"],
                        to: subjectCourses[i]["CourseCode"],
                        dashes: false,
                        arrows: "to",
                        color: {
                            color: color_dict[curr_prereq_type],
                            hover: color_dict[curr_prereq_type],
                        }, // set color to current pre-req type colour
                    });
                }

                // reset to "and" once outside bracket (this isn't totally correct but it's close enough)
                if (compiled[j]["type"] === "close_bracket") {
                    console.log("closed");
                    curr_prereq_type = "and";
                }
            }
        }

        // Show the road map on the webpage at the element subject-roadmap
        data = {
            nodes: new vis.DataSet(course_nodes),
            edges: new vis.DataSet(course_edges),
        };



        loader.addClass('hidden');
        //Create the network
        network = new vis.Network(container, data, network_options);
        setNetworkEvents();
    });

    function setNetworkEvents() {
        // Network on Node Select
        network.on("selectNode", function (event) {
            if (isToggled) {
                for (edge of event["edges"]) {
                    network.clustering.updateEdge(edge, {
                        hidden: true,
                    });
                }
            }
            for (edge of event["edges"]) {
                network.clustering.updateEdge(edge, {
                    hidden: false,
                });
            }

            // call function to display information for current node
            let curNodeID = event["nodes"][0];
            displaySelectNode(curNodeID);
            displayCreditCount(curNodeID);
        });

        // Network on Node Deselect
        network.on("deselectNode", function (event) {
            if (isToggled) {
                for (edge of event["previousSelection"]["edges"]) {
                    network.clustering.updateEdge(edge["id"], {
                        hidden: true,
                    });
                }
            }
            for (edge of event["previousSelection"]["edges"]) {
                network.clustering.updateEdge(edge["id"], {
                    hidden: false,
                });
            }
        });
    }

    // funtion to display total credits of all selected courses
    async function displayCreditCount(courseCode) {
        try {
            const response = await axios.get(
                `${API_ENDPOINT}course/${courseCode}`
            );
            if (response.data) {
                const courseData = response.data[0];

                if (!creditList.includes(courseData.CourseName)) {
                    creditList.push(courseData.CourseName);
                    var counterElement = document.getElementById('counter');
                    credit = credit + courseData.CourseWeight;

                    counterElement.textContent  = credit;
                    console.log(creditList);
                    displayCreditList();
                }
            }
        } catch (error) {
            // Handle errors
            alert("Warning:\nCourse not Found in Database!");
            console.error(error);
        }
    }

    function displayCreditList() {
        let displayedInfo = document.createElement("p");
        displayedInfo.classList.add("p4");

        for (i = 0; i < creditList.length; i++) {
            displayedInfo.innerHTML += "<b>Course Code: </b>";
            let newText = document.createTextNode(creditList[i]);
            displayedInfo.appendChild(newText);
            displayedInfo.innerHTML += "<br>";
            console.log(newText);
        }


        listContainer.innerHTML = "";
        listContainer.appendChild(displayedInfo);
    }

    // function to display selected node's course information
    async function displaySelectNode(courseCode) {
        try {
            // API Call to fetch course information from our API
            const response = await axios.get(
                `${API_ENDPOINT}course/${courseCode}`
            );

            if (response.data) {
                const courseData = response.data[0];

                let displayedInfo = document.createElement("p");
                displayedInfo.classList.add("p4");
                displayedInfo.innerHTML += "<b>Course Code: </b>";
                let newText = document.createTextNode(courseCode);

                // APENDING INFORMATION TO ELEMENT (LOOK INTO MORE EFICIENT/BETTER WAY TO DO THIS)
                // append course ID
                displayedInfo.appendChild(newText);
                displayedInfo.innerHTML += "<br>";

                // append course Name
                displayedInfo.innerHTML += "<b>Course Name: </b>";
                newText = document.createTextNode(courseData.CourseName);
                displayedInfo.appendChild(newText);
                displayedInfo.innerHTML += "<br>";

                // append course description
                displayedInfo.innerHTML += "<b>Description: </b>";
                newText = document.createTextNode(courseData.CourseDescription);
                displayedInfo.appendChild(newText);
                displayedInfo.innerHTML += "<br>";

                // append course department
                displayedInfo.innerHTML += "<b>Department: </b>";
                newText = document.createTextNode(courseData.Department);
                displayedInfo.appendChild(newText);
                displayedInfo.innerHTML += "<br>";

                // append course weight
                displayedInfo.innerHTML += "<b>Course Weight: </b>";
                newText = document.createTextNode(courseData.CourseWeight);
                displayedInfo.appendChild(newText);
                displayedInfo.innerHTML += "<br>";

                // append course prerequisite credits
                displayedInfo.innerHTML += "<b>Required Credits: </b>";
                newText = document.createTextNode(
                    courseData.PrerequisiteCredits
                );
                displayedInfo.appendChild(newText);
                displayedInfo.innerHTML += "<br>";

                // append course prerequisites
                displayedInfo.innerHTML += "<b>Required Courses: </b>";
                newText = document.createTextNode(courseData.Prerequisites);
                displayedInfo.appendChild(newText);
                displayedInfo.innerHTML += "<br>";

                // append course restrictions
                displayedInfo.innerHTML += "<b>Restricted Courses: </b>";
                newText = document.createTextNode(courseData.Restrictions);
                displayedInfo.appendChild(newText);
                displayedInfo.innerHTML += "<br>";

                // append course offering
                displayedInfo.innerHTML += "<b>Offerings: </b>";
                newText = document.createTextNode(courseData.CourseOffering);
                displayedInfo.appendChild(newText);
                displayedInfo.innerHTML += "<br>";

                detailsContainer.innerHTML = "";
                detailsContainer.appendChild(displayedInfo);
            }
        } catch (error) {
            // Handle errors
            alert("Warning:\nCourse not Found in Database!");
            console.error(error);
        }
    }
});

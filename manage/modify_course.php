<?php

require_once __DIR__ . '/../auth/mysql_config.php';
require_once __DIR__ . '/../course/course.php';
require_once __DIR__ . '/../header/auth_header.php';

if (!isset($_POST['action'], $_POST['course'])) {
    exit("Post params not set");
}

if (!$userRole->HasPermission("manage_courses")) {
    header("Location: user-home.php");
}

$course = $_POST['course'];
$action = $_POST['action'];

switch ($action) {
    case 'delete':
        return TryDeleteCourse($course);
    case 'add':
        if (!isset($_POST['description'])) {
            exit("Description not set");
        }
        $description = $_POST['description'];
        return TryAddCourse($course, $description);
}

function TryDeleteCourse($courseId) {
    
    // TODO - Ensure no users are assigned to the course first.
    
    $response = array();
    if (!Course::CourseWithIdExists($courseId)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't delete course as the course doesn't exist";
        echo json_encode($response);
        return FALSE;
    } else {
        Course::DeleteCourseWithId($courseId);
        $response['success'] = TRUE;
        $response['message'] = "Successfully deleted course";
        echo json_encode($response);
        return TRUE;
    }
}

function TryAddCourse($courseName, $courseDescription) {
    $response = array();

    if (Course::CourseWithNameExists($courseName)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't add course as a course with this name already exists";
        echo json_encode($response);
        return FALSE;
    } else {
        Course::AddCourseWithNameAndDescription($courseName, $courseDescription);
        //$course = Course::GetCourseWithName($courseName);
        
        $response['success'] = TRUE;
        $response['message'] = "Successfully added course";
        echo json_encode($response);
        return TRUE;
    }
}
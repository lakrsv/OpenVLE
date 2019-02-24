<?php

require_once __DIR__ . '/../auth/mysql_config.php';
require_once __DIR__ . '/../classes/course.php';
require_once __DIR__ . '/../classes/courseSection.php';
require_once __DIR__ . '/../classes/courseSectionContent.php';
require_once __DIR__ . '/../header/auth_header.php';

$action = filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING);

$canManageCourses = $userRole->HasPermission("manage_courses");
$canAddAssignment = $userRole->HasPermission("add_assignment");
$canAddQuiz = $userRole->HasPermission("add_quiz");
$canAddResource = $userRole->HasPermission("add_resource");

$canAddSection = $canManageCourses || $canAddAssignment || $canAddQuiz || $canAddResource;
$canAddContent = $canManageCourses || $canAddAssignment || $canAddQuiz || $canAddResource;

if (!$action) {
    exit("Post params not set");
}

switch ($action) {
    case 'delete':
        if (!$canManageCourses) {
            header("Location: user-home.php");
        }
        $course = filter_input(INPUT_POST, "course", FILTER_SANITIZE_STRING);
        if (!$course) {
            exit("Course not set");
        }
        return TryDeleteCourse($course);
    case 'add':
        if (!$canManageCourses) {
            header("Location: user-home.php");
        }
        $course = filter_input(INPUT_POST, "course", FILTER_SANITIZE_STRING);
        if (!$course) {
            exit("Course not set");
        } $description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
        if (!$description) {
            exit("Description not set");
        }
        return TryAddCourse($course, $description);
    case 'delete-section':
        if (!$canAddSection) {
            header("Location: user-home.php");
        }
        $section = filter_input(INPUT_POST, "section", FILTER_SANITIZE_NUMBER_INT);
        if (!$section) {
            exit("Section not set");
        }
        return TryDeleteSection($section);
    case 'delete-content':
        if (!$canAddContent) {
            header("Location: user-home.php");
        }
        $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_NUMBER_INT);
        if (!$content) {
            exit("Content not set");
        }
        return TryDeleteContent($content);
}

function TryDeleteCourse($courseId) {
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

function TryDeleteSection($sectionId) {
    $response = array();
    if (!CourseSection::SectionWithIdExists($sectionId)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't delete section as the section doesn't exist";
        echo json_encode($response);
        return FALSE;
    } else {
        CourseSection::DeleteSectionWithId($sectionId);
        $response['success'] = TRUE;
        $response['message'] = "Successfully deleted section";
        echo json_encode($response);
        return TRUE;
    }
}

function TryDeleteContent($contentId) {
    $response = array();
    if (!CourseSectionContent::ContentWithIdExists($contentId)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't delete content as the content doesn't exist";
        echo json_encode($response);
        return FALSE;
    } else {
        CourseSectionContent::DeleteContentWithId($contentId);
        $response['success'] = TRUE;
        $response['message'] = "Successfully deleted content";
        echo json_encode($response);
        return TRUE;
    }
}

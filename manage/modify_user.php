<?php

require_once __DIR__ . '/../auth/mysql_config.php';
require_once __DIR__ . '/../auth/role.php';
require_once __DIR__ . '/../auth/login.php';
require_once __DIR__ . '/../header/auth_header.php';
require_once __DIR__ . '/../classes/course.php';

if (!isset($_POST['action'], $_POST['user'])) {
    exit("Post params not set");
}

if (!$userRole->HasPermission("manage_users")) {
    header("Location: user-home.php");
}

$user = $_POST['user'];
$action = $_POST['action'];

switch ($action) {
    case 'delete':
        return TryDeleteUser($user);
    case 'add':
        if (!isset($_POST['password'], $_POST['name'])) {
            exit("Post params not set");
        }
        $password = $_POST['password'];
        $userName = $_POST['name'];
        return TryAddUser($user, $userName, $password);
    case 'change-role':
        $roleName = filter_input(INPUT_POST, "roleName", FILTER_SANITIZE_STRING);
        if (!$roleName) {
            exit("roleName not set");
        }
        $roleId = Role::GetRoleIdFromRoleName($_POST["roleName"]);
        return TryChangeUserRole($user, $roleId);
    case 'unassign-course':
        $courseId = filter_input(INPUT_POST, "course", FILTER_SANITIZE_NUMBER_INT);
        if (!$courseId) {
            exit("courseId not set");
        }
        return TryUnassignCourse($user, $courseId);
    case 'assign-course':
        $courseId = filter_input(INPUT_POST, "course", FILTER_SANITIZE_NUMBER_INT);
        if (!$courseId) {
            exit("courseId not set");
        }
        return TryAssignCourse($user, $courseId);
}

function TryDeleteUser($userId) {
    $response = array();
    if (!User::UserWithIdExists($userId)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't delete user as the user doesn't exist";
        echo json_encode($response);
        return FALSE;
    } else {
        User::DeleteUserWithId($userId);
        $response['success'] = TRUE;
        $response['message'] = "Successfully deleted user";
        echo json_encode($response);
        return TRUE;
    }
}

function TryAddUser($email, $name, $password) {
    $response = array();

    if (strlen($password) < passwordConstants::$MIN_PASSWORD_LENGTH) {
        $response['success'] = FALSE;
        $response['message'] = "Password must be atleast ".passwordConstants::$MIN_PASSWORD_LENGTH." characters";
        echo json_encode($response);
        return FALSE;
    }

    if (User::UserWithEmailExists($email)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't add user as a user with this email already exists";
        echo json_encode($response);
        return FALSE;
    } else {
        User::AddUserWithEmailAndNameAndPassword($email, $name, $password);
        $userId = User::GetUserIdFromEmail($email);
        User::ChangeUserRole($userId, Role::GetRoleIdFromRoleName("learner"));

        $response['success'] = TRUE;
        $response['message'] = "Successfully added user";
        echo json_encode($response);
        return TRUE;
    }
}

function TryChangeUserRole($userId, $roleId) {
    $response = array();
    if (!User::UserWithIdExists($userId)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't change role for user that does not exist";
        echo json_encode($response);
        return FALSE;
    } else {
        User::ChangeUserRole($userId, $roleId);
        $response['success'] = TRUE;
        $response['message'] = "Successfully changed role of user";
        echo json_encode($response);
        return TRUE;
    }
}

function TryUnassignCourse($userId, $courseId) {
    $response = array();
    if (!User::UserWithIdExists($userId)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't unassign course for user that does not exist";
        echo json_encode($response);
        return FALSE;
    } else if (!Course::CourseWithIdExists($courseId)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't unassign course that doesn't exist";
        echo json_encode($response);
        return FALSE;
    } else {
        User::UnassignUserCourse($userId, $courseId);
        $response['success'] = TRUE;
        $response['message'] = "Unassigned course from user";
        echo json_encode($response);
        return TRUE;
    }
}

function TryAssignCourse($userId, $courseId) {
    $response = array();
    if (!User::UserWithIdExists($userId)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't assign course for user that does not exist";
        echo json_encode($response);
        return FALSE;
    } else if (!Course::CourseWithIdExists($courseId)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't assign course that doesn't exist";
        echo json_encode($response);
        return FALSE;
    } else if (User::HasCourse($userId, $courseId)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't assign course because user already has it";
        echo json_encode($response);
        return FALSE;
    } else {
        User::AssignUserCourse($userId, $courseId);
        $response['success'] = TRUE;
        $response['message'] = "Assigned course to user";
        echo json_encode($response);
        return TRUE;
    }
}

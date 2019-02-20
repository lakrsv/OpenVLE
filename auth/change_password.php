<?php

require_once 'mysql_config.php';
require_once 'smtp_config.php';
require_once 'login.php';
require_once __DIR__ . '/../classes/passwordConstants.php';

class ChangePassword {

    public static function SendResetPasswordEmail($userId) {

        $url = ChangePassword::GetResetPasswordLink($userId);

        $email = User::GetEmailFromId($userId);
        $subject = "OpenVLE - Your Password Reset Link";

        $body = "<p>You asked us to reset your password. You can find the link to reset your password below. ";
        $body .= "If this wasn't you, you can ignore this email</p>";
        $body .= "<p>Your password reset link:</br>";
        $body .= sprintf('<a href="http://%s/OpenVLE/%s">%s</a></p>', $_SERVER['HTTP_HOST'], $url, "Click to reset your password");

        SmtpConfig::SendMail($email, $subject, $body);
    }

    public static function GetResetPasswordLink($userId) {
        $token1 = bin2hex(random_bytes(8));
        $token2 = bin2hex(ChangePassword::CreateToken($userId));

        $expires_at = new DateTime();
        $expires_at->format('Y-m-d H:i:s');
        $expires_at->add(new DateInterval("PT15M")); //Expires in 15 Minutes.

        $connection = MysqlConfig::Connect();
        $sql = "INSERT INTO ResetPasswordTokens (userId, token1, token2, expires_at) VALUES (:userid, :token1, :token2, :expires_at)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId);
        $statement->bindValue("token1", $token1);
        $statement->bindValue("token2", $token2);
        $statement->bindValue("expires_at", $expires_at->format('Y-m-d H:i:s'));
        $statement->execute();

        ChangePassword::DeleteOldTokens($token1, $token2);

        $url = sprintf('change_password.php?%s', http_build_query([
            'token1' => $token1,
            'token2' => $token2
        ]));

        return $url;
    }

    public static function AreTokensValid($token1, $token2) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT expires_at FROM ResetPasswordTokens WHERE token1=:token1 AND token2=:token2 LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("token1", $token1);
        $statement->bindValue("token2", $token2);
        $statement->execute();

        $expires_at = $statement->fetchColumn();

        if (!$expires_at) {
            return FALSE;
        }

        $expires_at = date_create_from_format('Y-m-d H:i:s', $expires_at);


        $now = new DateTime();
        $now->format('Y-m-d H:i:s');

        if ($now > $expires_at) {
            $connection = MysqlConfig::Connect();
            $sql = "DELETE FROM ResetPasswordTokens WHERE token1=:token1 AND token2=:token2 LIMIT 1";
            $statement = $connection->prepare($sql);
            $statement->bindValue("token1", $token1);
            $statement->bindValue("token2", $token2);
            $statement->execute();

            return FALSE;
        }

        return TRUE;
    }

    public static function GetUserIdFromTokens($token1, $token2) {
        if (!ChangePassword::AreTokensValid($token1, $token2)) {
            return NULL;
        }

        $connection = MysqlConfig::Connect();
        $sql = "SELECT userId FROM ResetPasswordTokens WHERE token1 = :token1 AND token2 = :token2 LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("token1", $token1);
        $statement->bindValue("token2", $token2);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public static function DeleteOldTokens($newToken1, $newToken2) {
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM ResetPasswordTokens WHERE token1 NOT IN (:newtoken1) AND token2 NOT IN (:newtoken2)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("newtoken1", $newToken1);
        $statement->bindValue("newtoken2", $newToken2);
        $statement->execute();
    }

    public static function DeleteTokens($token1, $token2) {
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM ResetPasswordTokens WHERE token1=:token1 AND token2=:token2 LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("token1", $token1);
        $statement->bindValue("token2", $token2);
        $statement->execute();
    }

    private static function CreateToken($userId) {
        $token = $userId . random_bytes(32) . bin2hex(random_bytes(16));
        return password_hash($token, PASSWORD_DEFAULT);
    }

}

$email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
if ($email) {
    $userId = User::GetUserIdFromEmail($email);
    ChangePassword::SendResetPasswordEmail($userId);
}

if (!isset($_POST['token1'], $_POST['token2'])) {
    return;
}

$token1 = $_POST['token1'];
$token2 = $_POST['token2'];

$response = array();

if (!ChangePassword::AreTokensValid($token1, $token2)) {
    $response['success'] = FALSE;
    $response['message'] = 'This password reset request has expired. Please try again.';
    echo json_encode($response);
    die();
}

$newPassword = filter_input(INPUT_POST, "newPassword", FILTER_SANITIZE_STRING);
$confirmPassword = filter_input(INPUT_POST, "confirmPassword", FILTER_SANITIZE_STRING);

if (strcmp($newPassword, $confirmPassword)) {
    $response['success'] = FALSE;
    $response['message'] = 'The supplied passwords do not match';
    echo json_encode($response);
    die();
}

if (strlen($newPassword) < passwordConstants::$MIN_PASSWORD_LENGTH) {
    $response['success'] = FALSE;
    $response['message'] = "Password must be atleast " . passwordConstants::$MIN_PASSWORD_LENGTH . " characters";
    echo json_encode($response);
    die();
}

$userId = ChangePassword::GetUserIdFromTokens($token1, $token2);
if (!$userId) {
    $response['success'] = FALSE;
    $response['message'] = "Something went wrong when trying to reset your password";
    echo json_encode($response);
    die();
}

User::ChangePasswordForUser($userId, $newPassword);
ChangePassword::DeleteTokens($token1, $token2);

$response['success'] = TRUE;
$response['message'] = "Successfully changed password! Please login to continue";
echo json_encode($response);

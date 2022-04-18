<?php
require('db_connect/connect.php');
$action = $userData = '';
$response = array('code' => 200, 'message' => '', 'data' => json_encode(array()));

if (!isset($_REQUEST['action'])) {
    $response['code'] = 500;
    $response['message'] = 'no action specified.';
    die(json_encode($response));
} else {
    $action = $_REQUEST['action'];
}

if ($intent == 'user') {
    //User actions
    if ($action == 'addUser') {

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            require_once('user/UserFunctions.php');
            // check if user already exist..
            // echo count(get_object_vars(json_decode(getUser($conn, 0, $_POST['Phone'])))) <= 1 ? 'true' : 'false';
            if (count(get_object_vars(json_decode(getUser($conn, 0, $_POST['Phone'])))) <= 1) {
                $newUser = addUser($conn, $_POST);
                if (count(get_object_vars(json_decode($newUser))) > 0) {
                    $response['code'] = 200;
                    $response['message'] = 'User added successfully.';
                    $response['data'] = json_decode($newUser);
                } else {
                    $response['code'] = 400;
                    $response['message'] = 'Error adding user.' . $newUser;
                }
            } else {
                $response['code'] = 400;
                $response['message'] = 'user with this phone already exist.';
            }
        } else {
            $response['code'] = 500;
            $response['message'] = 'Bad request. No user data specified.';
            // die(json_encode($response));
        }
        echo (json_encode($response));
    } else if ($action === 'getUser') {
        //get user with id.
        require_once('user/UserFunctions.php');

        $Phone = $userId = '';
        if (isset($_GET['Phone'])) {
            $phone = $_GET['Phone'];
        }
        if (isset($_GET['userId'])) {
            $userId = $_GET['userId'];
        }
        echo ((getUser($conn, $userId, $Phone)));
    } else if ($action === 'getAllUsers') {
        require_once('user/UserFunctions.php');
        echo getAllUsers($conn);
    } else if ($action === 'loginUser') {
        require_once('user/UserFunctions.php');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $phone = $otpStatus = '';

            if (isset($_POST['Phone'])) {
                $phone = $_POST['Phone'];
            }
            if (isset($_POST['otpStatus'])) {
                $otpStatus = $_POST['otpStatus'];
            }

            if (!empty($phone) && !empty($otpStatus)) {
                session_start();
                $login = loginUser($conn, $phone, $otpStatus);
                foreach (get_object_vars(json_decode($login)) as $key => $value) {
                    $_SESSION[$key] = $value;
                    // echo ($key . " " . $value . "\n");
                };
                $response['code'] = 200;
                $response['message'] = 'User logged In successfully.';
                $response['data'] = json_decode($login);
            } else {
                $response['code'] = 500;
                $response['message'] = 'Bad Request. Phone and OTP status can\'t be empty';
            }
        } else {
            $response['code'] = 500;
            $response['message'] = 'Bad Request. Use POST method';
        }

        echo (json_encode($response));
    } else if ($action === 'logoutUser') {
        require_once('user/UserFunctions.php');
        $phone = '';
        if (isset($_REQUEST['Phone']) && !empty($_REQUEST['Phone'])) {
            $phone = $_REQUEST['Phone'];

            $logoutStatus = logoutUser($conn, $phone);

            if ($logoutStatus) {
                $response['code'] = 200;
                $response['message'] = 'User Logged Out successfuly';
            } else {
                $response['code'] = 200;
                $response['message'] = 'there was an error loggin out.';
            }
        } else {
            $response['code'] = 500;
            $response['message'] = 'Bad Request! no user provided for logout.';
        }

        echo (json_encode($response));
    } else if ($action === 'updateUser') {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            require_once('user/UserFunctions.php');
            $phone = $userId = '';
            $is_company = false;
            if (isset($_REQUEST['isCompany'])) {
                $is_company = $_REQUEST['isCompany'];
            }
            if (count(get_object_vars(json_decode(getUser($conn, 0, $_POST['Phone'])))) >= 1) {
                $updatedData = updateUser($conn, $_POST, $is_company);
                // echo ($updatedData);
                $response['code'] = 200;
                $response['message'] = 'user updated successfully.';
                $response['data'] = json_decode($updatedData);
            } else {
                $response['code'] = 400;
                $response['message'] = 'user with this phone dosn\'nt exist.';
            }
        } else {
            $response['code'] = 500;
            $response['message'] = 'Bad request. No user data specified.';
            // die(json_encode($response));
        }
        echo (json_encode($response));
    } else {
        $response['code'] = 400;
        $response['message'] = 'Requested endpoint not found.';
    }
} elseif ($intent == 'posts') {
    // Post actions.
}

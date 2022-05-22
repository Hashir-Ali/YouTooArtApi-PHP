<?php
require('db_connect/connect.php');
$action = $userData = $intent = '';
$response = array('code' => 200, 'message' => '', 'data' => json_encode(array()));

if (!isset($_REQUEST['action'])) {
    $response['code'] = 500;
    $response['message'] = 'no action specified.';
    die(json_encode($response));
} else {
    $action = $_REQUEST['action'];
}

if (!isset($_REQUEST['intent'])) {
    $response['code'] = 500;
    $response['message'] = 'no intent specified.';
    die(json_encode($response));
} else {
    $intent = $_REQUEST['intent'];
}

if ($intent  == 'user') {
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
    require_once('posts/postFunctions.php');
    // Post actions.
    if ($_REQUEST['action'] === "addPost") {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $postData = addPost($conn, $_POST);
            $response['code'] = 200;
            $response['message'] = 'User Post added successfully.';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 400;
            $response['message'] = 'Bad request.';
        }
        echo json_encode($response);
    } else if ($_REQUEST['action'] === 'updatePost') {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $postData = updatePost($conn, $_POST);
            $response['code'] = 200;
            $response['message'] = 'User post updated successfully.';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 400;
            $response['message'] = 'Bad Request, kindly use POST method.';
        }
        echo json_encode($response);
    } else if ($_REQUEST['action'] === "getPost") {
        $postData = getPost($conn, $_REQUEST['postId']);

        if (!empty(json_decode($postData))) {
            $response['code'] = 200;
            $response['message'] = 'success';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 400;
            $response['message'] = 'Bad Request.';
            $response['data'] = $postData;
        }
        echo (json_encode($response));
    } else if ($_REQUEST['action'] === 'deletePost') {
        if (isset($_REQUEST['postId'])) {
            if (deletePost($conn, $_REQUEST['postId'])) {
                $response['code'] = 200;
                $response['message'] = 'Post successfully deleted';
            } else {
                $response['code'] = 400;
                $response['message'] = 'Error updating post.';
            }
            echo (json_encode($response));
        }
    } else if ($_REQUEST['action'] === 'getAllPosts') {

        if (isset($_REQUEST['userId'])) {
            $userPost = getUserPosts($conn, $_REQUEST['userId']);

            $response['code'] = 200;
            $response['message'] = 'success';
            $response['data'] = json_decode($userPost);
        } else {
            $response['code'] = 400;
            $response['message'] = 'error';
        }
        echo (json_encode($response));
    } else if ($_REQUEST['action'] === 'addComment') {
        $comment = addComment($conn, $_POST);
        if (!empty($comment)) {
            $response['code'] = 200;
            $response['message'] = 'comment added successfully';
            $response['data'] = json_decode($comment);
        } else {
            $response['code'] = 400;
            $response['message'] = 'Error updating comments.';
        }
        // echo ("I am here.");
        echo (json_encode($response));
    } else if ($_REQUEST['action'] === 'deleteComment') {
        $comment = deleteComment($conn, $_REQUEST['comment_id']);
        if ($comment) {
            $response['code'] = 200;
            $response['message'] = 'comment deleted successfully';
        } else {
            $response['code'] = 400;
            $response['message'] = 'error deleting comment';
        }
        echo (json_encode($response));
    } else if ($_REQUEST['action'] === 'updateComment') {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $commentData = updateComment($conn, $_POST);
            if (!empty(json_decode($commentData))) {
                $response['code'] = 200;
                $response['message'] = 'Comment Updated successfuly.';
            } else {
                $response['code'] = 400;
                $response['message'] = 'Error: ' . $commentData;
            }
        } else {
            $response['code'] = 400;
            $response['message'] = 'Bad Request';
        }
        echo (json_encode($response));
    } else if ($_REQUEST['action'] === 'getPostComments') {
        $postComments = getPostComments($conn, $_REQUEST['postId']);

        if (!empty(json_decode($postComments))) {
            $response['code'] = 200;
            $response['message'] = 'success';
            $response['data'] = json_decode($postComments);
        } else {
            $response['code'] = 200;
            $response['message'] = 'No comments.';
            $response['data'] = [];
        }
        echo (json_encode($response));
    } else if ($_REQUEST['action'] === 'addCommentLike') {
        $addedComment = addCommentLike($conn, $_POST);
        if (count(get_object_vars(json_decode($addedComment))) >= 1) {
            $response['code'] = 200;
            $response['message'] = 'Comment added successfully.';
            $response['data'] = $addedComment;
        } else {
            $response['code'] = 500;
            $response['message'] = 'some error occured while adding the comment';
        }
        echo (json_encode($response));
    } else if ($_REQUEST['action'] === 'likePost') {
        $likedPost = likePost($conn, $_POST);

        if (count(get_object_vars(json_decode($likedPost))) >= 1) {
            $response['code'] = 200;
            $response['message'] = 'Post liked successfully.';
            $response['data'] = $likedPost;
        } else {
            $response['code'] = 400;
            $response['message'] = 'There was an error liking the post.';
        }
        echo json_encode($likedPost);
    } else if ($_REQUEST['action'] === 'unlikePost') {
        $unlikePost = unlikePost($conn, $_REQUEST['likeId']);

        if ($unlikePost) {
        } else {
        }
    } else {
        $response['code'] = 400;
        $response['message'] = 'Unknown action method.';
        $response['data'] = [];
        echo json_encode($response);
    }
} elseif ($intent == 'casting_calls') {
    if ($action == 'addCall') {
        $postData = (addJob($conn, $_POST));
        $response['code'] = 200;
        $response['message'] = 'Casting call added successfully.';
        $response['data'] = json_decode($postData);
    } else if ($action == 'applyToCall') {
        $postData = (applyToCall($conn, $_POST));
        if ($postData > 0) {
            $response['code'] = 200;
            $response['message'] = 'apply to call successfully.';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 500;
            $response['message'] = 'Some error try again';
            $response['data'] = [];
        }
    } else if ($action == 'approveApply') {
        $postData = (acceptApply($conn, $_POST));
        if ($postData > 0) {
            $response['code'] = 200;
            $response['message'] = 'Approve apply successfully.';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 500;
            $response['message'] = 'There is an error approving apply try again';
            $response['data'] = [];
        }
    } else if ($action == 'declineApply') {
        $postData = (rejectApply($conn, $_POST));
        if ($postData > 0) {
            $response['code'] = 200;
            $response['message'] = 'reject apply successfully.';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 500;
            $response['message'] = 'some error occured while rejecting apply try again';
            $response['data'] = [];
        }
    } else if ($action == 'listJobs') {
        $postData = (listJobs($conn, $_POST));
        if ($postData > 0) {
            $response['code'] = 200;
            $response['message'] = 'list of job fetch successfully. ';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 500;
            $response['message'] = 'something wrong to show the list try again.';
            $response['data'] = [];
        }
    } else if ($action == 'listApplies') {
        $postData = (listApplies($conn, $_POST));
        if ($postData > 0) {
            $response['code'] = 200;
            $response['message'] = 'list of applies loaded successfully.';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 500;
            $response['message'] = 'some error occured plesae try again.';
            $response['data'] = [];
        }
    }
} elseif ($intent == 'friends') {
    require_once('friends/friendFunctions.php');
    if ($action == 'listFriends') {
        $postData = (listFriends($conn, $_REQUEST['userId']));
        $response['code'] = 200;
        $response['message'] = 'list has been loaded successfully.';
        $response['data'] = json_decode($postData);
        echo json_encode($response);
    } else if ($action == 'friendRequests') {
        $postData = (friendRequests($conn, $_REQUEST['userId']));
        $response['code'] = 200;
        $response['message'] = 'Friend request list';
        $response['data'] = json_decode($postData);
        echo (json_encode($response));
    } else if ($action == 'sendRequest') {
        $postData = (sendRequest($conn, $_POST));
        if ($postData > 0) {
            $response['code'] = 200;
            $response['message'] = 'friend request sended successfully.';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 500;
            $response['message'] = 'while sending request have some problem please try again';
            $response['data'] = [];
        }
        echo json_encode($response);
    } else if ($action == 'acceptFriendRequest') {
        $postData = (acceptFriendRequest($conn, $_POST));
        if ($postData > 0) {
            $response['code'] = 200;
            $response['message'] = 'friend request accepted successfully.';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 500;
            $response['message'] = 'while accepting request have some problem please try again';
            $response['data'] = [];
        }
        echo json_encode($response);
    } else if ($action == 'rejectFriendRequest') {
        $postData = (rejectFriendRequest($conn, $_POST));
        if ($postData > 0) {
            $response['code'] = 200;
            $response['message'] = 'friend request rejected successfully.';
            $response['data'] = json_decode($postData);
        } else {
            $response['code'] = 500;
            $response['message'] = 'while rejecting request have some problem please try again';
            $response['data'] = [];
        }
        echo json_encode($response);
    } else {
        $response['code'] = 500;
        $response['message'] = 'Unknown action.';
        $response['data'] = '';
        echo json_encode($response);
    }
}

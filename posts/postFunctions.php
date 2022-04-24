<?php

function addPost($conn, $postData)
{
    $SQL = "INSERT INTO `posts`( `posted_by`, `post_text`, `post_url`) VALUES (?,?,?)";
    $stmt = $conn->prepare($SQL);
    $stmt->bind_param('iss', $postData['posted_by'], $postData['post_text'], $postData['post_url']);

    if ($stmt->execute()) {
        // return $stmt->insert_id;
        return getPost($conn, $stmt->insert_id);
    } else {
        return $stmt->error;
    }
}
function updatePost($conn, $postData)
{
    $sql = "UPDATE `posts` SET `post_text`=?,`post_url`=? WHERE `post_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $postData['post_text'], $postData['post_url'], $postData['post_id']);

    if ($stmt->execute()) {
        return getPost($conn, $postData['post_id']);
    } else {
        return json_encode(array());
    }
}
function getPost($conn, $postId)
{
    $sql = "SELECT * FROM `posts` WHERE `post_id`= " . $postId;
    $stmt = $conn->query($sql);
    if ($stmt->num_rows > 0) {
        return json_encode($stmt->fetch_assoc());
    } else {
        return json_encode(array());
    }
}
function deletePost($conn, $postId)
{
    $sql = "DELETE FROM `posts` WHERE `post_id` = " . $postId;
    $result = $conn->query($sql);

    if ($result) {
        return true;
    } else {
        return false;
    }
}
function getUserPosts($conn, $userId)
{
    $postArray = array();
    $SQL = "SELECT * FROM `posts` WHERE `posted_by` = " . $userId;
    $result = $conn->query($SQL);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($postArray, $row);
        }
        return json_encode($postArray);
    } else {
        return json_encode(array());
    }
}
function addComment($conn, $commentData)
{
    $sql = "INSERT INTO `post_comments`(`post_id`, `commented_by`, `commented_text`) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param('iis', $commentData['post_id'], $commentData['commented_by'], $commentData['commented_text']);

    if ($stmt->execute()) {
        // return ($stmt->insert_id);
        return getPostComments($conn, $commentData['post_id']);
    } else {
        return ($stmt->error);
    }
    return (0);
}
function deleteComment($conn, $commentId)
{
    $sql = "DELETE FROM `post_comments` WHERE `id` = " . $commentId;
    $result = $conn->query($sql);
    if ($result) {
        return true;
    } else {
        return false;
    }
}
//need get specific comment function..
function updateComment($conn, $commentData)
{
    $sql = "UPDATE `post_comments` SET `commented_text`= ? WHERE `id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $commentData['comment_text'], $commentData['comment_id']);
    if ($stmt->execute()) {
        return getPostComments($conn, $commentData['comment_id']);
    } else {
        return ($stmt->error);
    }
}
function getPostComments($conn, $postId)
{
    $commentsArray = array();
    $sql = "SELECT * FROM `post_comments` WHERE `post_id` = " . $postId . "";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($commentsArray, $row);
        }

        //append likes as well.
        return json_encode($commentsArray);
    } else {
        return json_encode(array());
    }
    return json_encode(array());
}
function addCommentLike($conn, $likeData)
{
    $sql = "INSERT INTO `comment_likes`(`post_id`, `comment_id`, `liked_by`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param('iii', $likeData['post_id'], $likeData['comment_id'], $likeData['liked_by']);

    if ($stmt->execute()) {
        return getPostComments($conn, $likeData['post_id']);
    } else {
        return json_encode(array());
    }
}

// get comment like
// unlike commend 
function likePost($conn, $likeData)
{
    $sql = "INSERT INTO `post_likes`(`post_id`, `user_id`) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $likeData['post_id'], $likeData['user_id']);

    if ($stmt->execute()) {
        return (getPost($conn, $likeData['post_id']));
    } else {
        return (json_encode(array()));
    }
}
// unlike post.
// get post likes

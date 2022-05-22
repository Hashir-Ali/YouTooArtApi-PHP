<?php

// list friends
function listFriends($conn, $userId)
{
    $listFriendArray = array();
    $sql = "SELECT * FROM `friends` WHERE `user_id` = " . $userId;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($listFriendArray, $row);
        }
        return json_encode($listFriendArray);
    } else {
        return json_encode(array());
    }
}
// list friend requests
function friendRequests($conn, $userId)
{
    $requestListArray = array();
    $sql = "SELECT * FROM `friend_request` WHERE `user_id` = " . $userId;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($requestListArray);
        }
        return json_encode($requestListArray);
    } else {
        return json_encode(array());
    }
}
// send friend request
function sendRequest($conn, $postData)
{
    $sendRequest = "INSERT INTO `friend_request`( `sent_by`, `sent_to`,  `status`) VALUES (?,?,2)";
    $stmt = $conn->prepare($sendRequest);
    $stmt->bind_param('ii', $postData['sent_by'], $postData['sent_to']);
    if ($stmt->execute()) {
        return $stmt->insert_id;
    } else {
        return 0;
    }
}
// accept friend request
function acceptFriendRequest($conn, $requestId)
{
    // only change status of request
    // apply_status will become 1
    $sql = "UPDATE `friend_request` SET `status` = 0 WHERE `id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $requestId);
    if ($stmt->execute()) {
        //select from friend_request table where id = ?";"
        //add to friends table
        //set sent_by to friend_Id in friends table
        //set sent_to to user_Id in friends table

        $sql = "SELECT * FROM `friend_request` WHERE `id`= " . $requestId;
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $sent_to = $row['sent_to'];
            $sent_by = $row['sent_by'];


            $sql = "INSERT INTO `friends`(`user_id`, `friend_id`) VALUES (?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $sent_to, $sent_by);

            if ($stmt->execute()) {
                return $stmt->insert_id;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// reject friend request
function rejectFriendRequest($conn, $requestId)
{
    // only change status of reuqest
    // apply_status will become 0
    $reject_request = "UPDATE `friend_request` SET `status` = 1 WHERE `id` = ?";
    $stmt = $conn->prepare($reject_request);
    $stmt->bind_param('i', $requestId);
    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}

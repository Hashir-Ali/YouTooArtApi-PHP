<?php

// send notification
function sendNotification($conn, $userId, $notificationType, $notificationText)
{
    $sql = "INSERT INTO `notifications`( `user_id`, `notification_type`, `notification_text`) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $userId, $notificationType, $notificationText);
    if ($stmt->execute()) {
        return $stmt->insert_id;
    } else {
        return 0;
    }
}
// list notifications
function listNotifications($conn, $userId)
{
    $notificationsArray = array();
    $sql = 'SELECT * FROM `notifications` WHERE `user_id` = ' . $userId;
    $stmt = $conn->query($sql);
    if ($stmt->num_rows > 0) {
        while ($row = $stmt->fetch_assoc()) {
            array_push($notificationsArray, $row);
        }
        return json_encode($notificationsArray);
    } else {
        return json_encode(array());
    }
}

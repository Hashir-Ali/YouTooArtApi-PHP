<?php
function addJob($conn, $postData)
{
    $sql = "INSERT INTO `casting_calls`(`uploaded_by`, `title`, `content_type`, `gender`, `starting_age`,
         `ending_age`, `city`, `shoot`, `remuneration_name`, `remuneration_amount`, `crew_req`, 
         `crew_type`, `expiry_date`, `requirements_text`, `payment_status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'isssiisssiissss',
        $postData['uploaded_by'],
        $postData['title'],
        $postData['content_type'],
        $postData['gender'],
        $postData['starting_age'],
        $postData['ending_age'],
        $postData['city'],
        $postData['shoot'],
        $postData['remuneration_name'],
        $postData['remuneration_amount'],
        $postData['crew_req'],
        $postData['crew_type'],
        $postData['expiry_date'],
        $postData['requirements_text'],
        $postData['payment_status']
    );
    if ($stmt->execute()) {
        // return $stmt->insert_id;
        return getJob($conn, $stmt->insert_id);
    } else {
        return json_encode(array());
    }
}

function getJob($conn, $jobId)
{
    $sql = "SELECT * FROM `casting_calls` WHERE `id` = " . $jobId;
    $stmt = $conn->query($sql);

    if ($stmt->num_rows > 0) {
        return json_encode(array($stmt->fetch_assoc()));
    } else {
        return json_encode(array());
    }
}


function applyToCall($conn, $postData)
{
    $sql = "INSERT INTO `casting_calls_applies`(
        `call_id`, `user_id`, `selection_text`, `audition_clip_url`,
        `apply_status`) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'iisss',
        $postData['call_id'],
        $postData['user_id'],
        $postData['selection_text'],
        $postData['audition_clip_url'],
        $postData['apply_status']
    );
    if ($stmt->execute()) {
        return $stmt->insert_id;
    } else {
        return json_encode($stmt->error);
    }
}

function acceptApply($conn, $id)
{
    $sql = "UPDATE `casting_calls_applies` SET `apply_status`= 1  WHERE `id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        return "";
    } else {
        return json_encode(array());
    }
}

function rejectApply($conn, $id)
{
    $sql = "UPDATE `casting_calls_applies` SET `apply_status`= 0  WHERE `id` = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        return 1;
    } else {
        return json_encode(array());
    }
}

function listJobs($conn, $crewTypeId)
{
    $jobsArray = array();
    $sql = "SELECT * FROM `casting_calls` WHERE `payment_status` = 1 AND `crew_type` = " . $crewTypeId;
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row =  $result->fetch_assoc()) {
            array_push($jobsArray, $row);
        }
        return json_encode($jobsArray);
    } else {
        return json_encode(array());
    }
}

function listApplies($conn, $jobId)
{
    $appliesArray = array();
    $sql = "SELECT * FROM `casting_calls_applies` WHERE `call_id` = " . $jobId;

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($appliesArray, $row);
        }
        return json_encode($appliesArray);
    } else {
        return json_encode(array());
    }
}

<?php

// add user: returns integer ( inserted user id or 0 )
function addUser($conn, $userData = array(), $isCompany = false)
{
	// return $userData['user_type'];
	if ($isCompany) {
		$SQL = 'INSERT INTO `users`(`user_type`, `Phone`, `Category`, `profile_picture`, `first_name`, `last_name`, `company_name`, `city`, `state`, `short_bio`, `contact_info`) VALUES (?,?,?,?,?,?,?,?,?,?,?)';

		$stmt = $conn->prepare($SQL);
		$stmt->bind_param('iiissssssss', $userData['user_type'], $userData['Phone'], $userData['Category'], $userData['profile_picture'], $userData['first_name'], $userData['last_name'], $userData['company_name'], $userData['city'], $userData['state'], $userData['short_bio'], $userData['contact_info']);
	} else {
		$SQL = 'INSERT INTO `users`(`user_type`, `Phone`, `Category`, `profile_picture`, `first_name`, `last_name`, `city`, `state`, `short_bio`) VALUES (?,?,?,?,?,?,?,?,?)';

		$stmt = $conn->prepare($SQL);
		$stmt->bind_param('isissssss', $userData['user_type'], $userData['Phone'], $userData['Category'], $userData['profile_picture'], $userData['first_name'], $userData['last_name'], $userData['city'], $userData['state'], $userData['short_bio']);
	}

	if ($stmt->execute()) {
		// return $stmt->insert_id;
		getUser($conn, $stmt->insert_id, '');
	} else {
		return $stmt->error; // none added
	}
	return 0; //none added
}

// update user: returns JSON ( user updated data or empty JSON )
function updateUser($conn, $userData = array(), $isCompany = false)
{

	if ($isCompany) {
		$SQL = 'UPDATE `users` SET `profile_picture`= ?,`first_name`= ?,`last_name`= ?,`company_name`= ?,`city`= ?,`state`=?,`short_bio`=?,`contact_info`=?,`feature_img_1`=?,`feature_img_2`=?,`feature_img_3`=?,`work_video`=?,`work_photos`=? WHERE `id` = ? or `Phone` = ?';

		$stmt = $conn->prepare($SQL);
		$stmt->bind_param('sssssssssssssis', $userData['profile_picture'], $userData['first_name'], $userData['last_name'], $userData['company_name'], $userData['city'], $userData['state'], $userData['short_bio'], $userData['contact_info'], $userData['feature_img_1'], $userData['feature_img_2'], $userData['feature_img_3'], $userData['work_video'], $userData['work_photos'], $userData['id'], $userData['Phone']);
	} else {
		$SQL = 'UPDATE `users` SET `profile_picture`= ?,`first_name`= ?,`last_name`= ?, `city`= ?,`state`=?,`short_bio`=?, `feature_img_1`=?,`feature_img_2`=?,`feature_img_3`=?,`work_video`=?,`work_photos`=? WHERE `id` = ? or Phone = ?';
		$stmt = $conn->prepare($SQL);
		$stmt->bind_param('sssssssssssis', $userData['profile_picture'], $userData['first_name'], $userData['last_name'], $userData['city'], $userData['state'], $userData['short_bio'], $userData['feature_img_1'], $userData['feature_img_2'], $userData['feature_img_3'], $userData['work_video'], $userData['work_photos'], $userData['id'], $userData['Phone']);
	}

	if ($stmt->execute()) {
		return getUser($conn, $userData['id'], $userData['Phone']);
	} else {
		return json_encode(array()); // none updated
	}

	return json_encode(array()); // none updated
}

// getUserDetails (id): returns JSON (user data or empty JSON)
function getUser($conn, $userId, $phone)
{
	$sql = "SELECT * FROM `users` WHERE `id` = " . $userId . " OR `Phone` = '" . $phone . "'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		return json_encode($result->fetch_assoc());
	} else {
		return json_encode(array());
	}
}

// list all users: returns JSON (all users data or empty JSON)
function getAllUsers($conn)
{
	$usersArray = array();
	$sql = "SELECT * FROM `users`";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// return json_encode($result->fetch_assoc());

		while ($row = $result->fetch_assoc()) {
			// $data['calls_dialed'].push($row['calls_dialed']);
			array_push($usersArray, $row);
		}
		return json_encode($usersArray);
	} else {
		return json_encode(array());
	}
}

// loginUser (phone, otpStatus) returns JSON (user data or empty JSON )
function loginUser($conn, $phone, $otpStatus)
{
	// sets is_active to true and returns user data.
	$SQL = 'UPDATE `users` SET `is_active`= 1 WHERE `Phone` =' . $phone;
	$result = $conn->query($SQL);
	if ($result && $otpStatus) {
		//user is logged in, send the specific user data.
		$userId = 0;
		return getUser($conn, $userId, $phone);
	} else {
		return json_encode(array());
	}

	// returned data will be saved in session on API end.
}

//logoutUser returns boolean (true on logout else false)
function logoutUser($conn, $phone)
{
	// sets is_active to false and returns true
	// destroy the session on API end.
	$SQL = 'UPDATE `users` SET `is_active`= 0 WHERE `Phone` =' . $phone;
	$result = $conn->query($SQL);
	if ($result) {
		//user is logged out, send true so that API can destroy the session.
		return true;
	} else {
		return false;
	}
}

// delete user returns boolean (true on deleted else false)
function deleteUser($conn, $userId)
{
	$sql = "DELETE FROM `users` WHERE `id` = " . $userId;
	$result = $conn->query($sql);
	if ($result) {
		return true;
	} else {
		return false;
	}
}

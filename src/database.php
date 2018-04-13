<?php
$servername = "localhost";
$username = "root";
$password = "root";
$database = "chat_socket";
// Create connection
// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if(!empty($_POST["username"])) {
	$username =  $_POST["username"];
	$created_at = date('Y-m-d H:i:s');
	$prev = "SELECT * FROM users WHERE username='$username'";
	$result = $conn->query($prev);
	$user_id = false;
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$user_id = $row["id"];
		}
		echo json_encode(array("status"=>true, "data"=>$user_id));
		exit;
	}
	else {
		$new = "INSERT INTO `users` (username, create_at) VALUES ('$username', '$created_at')";
		if($conn->query($new)) {
			echo json_encode(array("status" => true, "data"=>$conn->insert_id));
		}
		else
			echo json_encode(array("status" => false));
	}
}
?>
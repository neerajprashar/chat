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
else
	echo "Connected";

if(!empty($_POST["username"])) {
	$query = "INSERT INTO `users` (username, create_at) VALUES ($_POST['username'], 'time()')";
	if($conn->query($query)) {
		echo "User Created";
	}
	else
		echo "Error in creating user";
}
?>
 <?php
$servername = "localhost";
$username = "atk4_test";
# Inject correct password here under before launching
# $password;
$dbname = "atk4_test";
#print_r($_POST);
#print_r($_GET);
#$db = \atk4\data\Persistence::connect('mysql:dbname=atk4_test;host=localhost', 'atk4_test', 'pNXjbocxe06hBQXd');
if($_POST['datetime'] !=""){
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO `log` (`id`, `txt`) VALUES (NULL, '".serialize($_POST)."');";
if ($conn->query($sql) === TRUE) {
 // echo "New record created successfully";
} else {
 // echo "Error: " . $sql . "<br>" . $conn->error;
}


$sql2 = "INSERT INTO `Box` (`id`, `boxID`, `datetime`, `temperature`, `humidity`, `pressure`, `gas`, `PM2`, `PM10`) 
VALUES 
(NULL, '','".$_POST[datetime]."', '".$_POST[temperature]."','".$_POST[humidity]."', '".$_POST[pressure]."','".$_POST[gas]."' , '".$_POST[PM2]."', '".$_POST[PM10]."');";

$sql = "INSERT INTO `log` (`id`, `txt`) VALUES (NULL, '".$sql2."');";
if ($conn->query($sql) === TRUE) {
//  echo "New record created successfully";
} else {
//  echo "Error: " . $sql . "<br>" . $conn->error;
}


#$sql = "INSERT INTO MyGuests (firstname, lastname, email)
#VALUES ('John', 'Doe', 'john@example.com')";

if ($conn->query($sql2) === TRUE) {
//  echo "New record created successfully";
} else {
 // echo "Error: " . $sql2 . "<br>" . $conn->error;
}

$conn->close();
}
?>
 {201 : "Object created"}
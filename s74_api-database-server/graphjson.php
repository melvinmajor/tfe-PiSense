 <?php
$servername = "localhost";
$username = "atk4_test";
# Inject correct password here under before launching
# $password;
$dbname = "atk4_test";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM `Box` WHERE temperature>0 ";
$result= $conn->query($sql);

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
// Free result set
$result -> free_result();
$conn -> close();

?>
 
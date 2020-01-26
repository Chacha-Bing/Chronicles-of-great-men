<?php
require("./sql_config.php");
require("./Re_josn.php");

// Returns some of the great man's data
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 
if(!isset($_GET["name"])) return null;
$name=$_GET["name"];
$sql = "SELECT name,pict,det_info FROM greatman WHERE name = $name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $data=$result->fetch_assoc();
    echo JSON($data);
} else {
    echo null;
}
$conn->close();
?>
<?php
require("./sql_config.php");
require("./Re_josn.php");

// Used for the query button auxiliary query box to return the corresponding name

$array = array();
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
$findquery=$_GET["findinfo"];
if ($findquery == ""){
    echo null;
}
else {
    $sql = "SELECT distinct greatman.name,greatman.lon,greatman.lat from mytest.greatman 
            left join mytest.masterwork on mytest.masterwork.name=mytest.greatman.name
            WHERE mytest.greatman.name like $findquery or mytest.greatman.occu like $findquery
            or mytest.greatman.nation like $findquery or mytest.masterwork.w_name like $findquery
            or mytest.greatman.act_age like $findquery or mytest.greatman.gender like $findquery";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($data=$result->fetch_assoc()){
            $array[] = $data;
        }
        echo JSON($array);
    } else {
        echo null;
    }
}
$conn->close();
?>
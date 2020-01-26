<?php
      require("sql_config.php");
      $conn=mysqli_connect($mysql_server_name, $mysql_username,'') or die("error connecting");
      mysqli_query($conn,"set names 'utf8'");
      mysqli_select_db($conn,$mysql_database);
      
      // Age and works of great men
      $data="";
      $array= array();

      $act_name=$_GET["act_age"];
      $name=$_GET["name"];

      class User{
        public $act_avg_age;
        public $age;
        public $act_avg_write;
        public $write;
      }
      $user=new User();

      $bth_age=mysqli_query($conn,"SELECT bth_year  FROM greatman WHERE name=$name");
      $dth_age=mysqli_query($conn,"SELECT dth_year  FROM greatman WHERE name=$name");
      $bth_row = mysqli_fetch_array($bth_age,MYSQLI_ASSOC);
      $dth_row = mysqli_fetch_array($dth_age,MYSQLI_ASSOC);


      if($bth_row['bth_year']==9999 or $dth_row['dth_year']==9999)
      {
        $age=mysqli_query($conn,"SELECT (dth_year-dth_year)AS 年龄  FROM greatman WHERE name=$name");
      }
      else{
      $age=mysqli_query($conn,"SELECT (dth_year-bth_year)AS 年龄  FROM greatman WHERE name=$name");
      }

      $row = mysqli_fetch_array($age,MYSQLI_ASSOC);
      $user->age=$row['年龄'];

      $act_avg_age=mysqli_query($conn,"SELECT AVG(dth_year-bth_year) AS 平均年龄 FROM greatman WHERE act_age=$act_name AND dth_year<9999 AND bth_year<9999 ");
      $row = mysqli_fetch_array($act_avg_age,MYSQLI_ASSOC);
      $user->act_avg_age=$row['平均年龄'];

      
      $act_avg_write=mysqli_query($conn,"SELECT COUNT(DISTINCT masterwork.w_name)/COUNT(DISTINCT greatman.NAME) AS 同时代平均作品数 FROM greatman LEFT JOIN masterwork ON masterwork.name=greatman.name  WHERE greatman.act_age=$act_name
      ");
      $row = mysqli_fetch_array($act_avg_write,MYSQLI_ASSOC);
      $user->act_avg_write=$row['同时代平均作品数'];


      $write=mysqli_query($conn,"SELECT COUNT(*) FROM masterwork WHERE name=$name");
      $row = mysqli_fetch_array($write,MYSQLI_ASSOC);
      $user->write=$row['COUNT(*)'];


      $array[]=$user;
      $data=json_encode($array);
      echo $data;
    ?>
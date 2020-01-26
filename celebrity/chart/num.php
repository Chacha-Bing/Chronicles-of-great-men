<?php
      require("sql_config.php");
      $conn=mysqli_connect($mysql_server_name, $mysql_username,'') or die("error connecting");
      mysqli_query($conn,"set names 'utf8'");
      mysqli_select_db($conn,$mysql_database);

      //Number of male and female contemporaries
      $act_name=$_GET["act_age"];
      $data="";
      $array= array();

    
      class User{
        public $name;
        public $value;
        public $selected;
      }
      $user=new User();
      
      $a=mysqli_query($conn,"SELECT act_age,COUNT(*) FROM greatman GROUP BY act_age");
      while($row = mysqli_fetch_array($a,MYSQLI_ASSOC)){
        $user=new User();
        if($row['act_age']==$act_name)
        {
          $user->value=$row['COUNT(*)'];
          $user->name=$row['act_age'];
          $user->selected=true;
          $array[]=$user;
        }
        else{
        $user->value=$row['COUNT(*)'];
        $user->name=$row['act_age'];
        $user->selected=false;
        $array[]=$user;
        }
      }
      $data=json_encode($array);
      echo $data;
    ?>
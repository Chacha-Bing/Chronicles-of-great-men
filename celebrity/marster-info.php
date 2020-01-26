<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>marster-info</title>
	<link rel="stylesheet" href="../lib/layui-v2.5.5/layui/css/layui.css">
	<link rel="stylesheet" href="../JQMp3CoolListPlayer/css/reset.css">
	<link rel="stylesheet" href="../JQMp3CoolListPlayer/css/style.css" media="screen" type="text/css" />
	<script src="../lib/echarts.min.js"></script>
	<script src="../lib/layui-v2.5.5/layui/layui.js"></script>
	<script src="../lib/jquery-1.12.4-min.js"></script>
	<style>
	@font-face {
		font-family: 'simpleKai';
		src: url("../src/方正北魏楷书简体.ttf")
    }
    html,body {
		height: 100%;
		margin: 0;
		padding: 0;
		text-align:center;
		background-image: url('../src/background.jpg');
		background-attachment: fixed;
		background-size: 100% 100%;
	}
	.para-title {
		text-align: left;
		font-size: 30px;
		padding: 0 8px 0 18px;
		margin: 35px 0 15px -30px;
		display: block;
		border-left: 12px solid #118e82;
	}
	.text-info {
		font-family: simpleKai,cursive;
		text-align: left;
		text-indent: 2em;
		padding: 12px;
		line-height: 22px;
		font-size: 21px;
	}
    .infobox {
		position: absolute;
		left: 5%;
		width: 550px;
		animation: bounceInLeft 2s;
	}
	.close-btn {
		position: absolute;
		top: 0;
		right: 0;
		height: 33px;
	}
	.no-see {
		display: none;
	}
	.font-cursive {
		color: black;
		font-family: Times,cursive;
	}
	.name {
		text-align: center;
		color: #011f3c;
		font-size: 35px;
		padding: 15px;
	}
	.layui-table {
		font-family: Times,simpleKai,cursive;
		color: black;
		text-align: left;
		background-color: #3c366729;
		color: #ffead2;
	}
	.layui-table td, .layui-table th {
		border-style: dashed;
		border-color: #9c8f89;
		font-size: 17px;
	}
	.layui-table tr {
		background-color: #ffffff00;
	}
	.layui-table tbody tr:hover {
		background-color: #777b87;
	}
	@keyframes bounceInLeft {
		0% {opacity: 0;transform: translate3d(-50px, 0, 0);}
		100% {opacity: 1;transform: none;}
    }
	.layui-elem-quote {
		margin-top: 60px;
		margin-bottom: 10px;
	}

	.chartbox {
		position: absolute;
		height: 100%;
		right: 5%;
		width: calc(90% - 550px);
		padding: 50px;
	}
	.chart {
		height: 100%;
	}
	</style>
</head>
<body>

	<?php
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "mytest";
		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
			die("连接失败: " . $conn->connect_error);
		} 
		if(!isset($_GET["name"])) return null;
		$name=$_GET["name"];
		
		$array_work = array();
		$sql_work = "SELECT w_name,w_type,w_time,w_pict,w_info FROM masterwork WHERE name = $name";
		$result_work = $conn->query($sql_work);
		if ($result_work->num_rows > 0) {
			while($data_work=$result_work->fetch_assoc()){
				$array_work[] = $data_work;
			}
		}

		$sql_person = "SELECT name,act_age,pict,det_info,ach FROM greatman WHERE name = $name";
		$result_person = $conn->query($sql_person);
		if ($result_person->num_rows > 0) {
			$data_person=$result_person->fetch_assoc();
		}

	?>

	<div id="infopane" class="infobox">
		<p class='name font-cursive'><?php echo $data_person["name"] ?></p>
		<img src=<?php echo $data_person["pict"] ?> />
		
		<div id='masterTable'>
			<p class='para-title font-cursive'>主要作品</p>
			<table class="layui-table">
				<colgroup>
					<col />
					<col />
					<col />
				</colgroup>
				<thead>
					<tr style="background-color: #48536d6b;">
						<th>著作</th>
						<th>时间</th>
						<th>标签</th>
					</tr> 
				</thead>
				<tbody id="masterBody">
					
				</tbody>
			</table>
		</div>

		<div id='baseinfo'>
			<p class='para-title font-cursive'>基本信息</p>
			<p class="text-info"><?php echo $data_person["det_info"] ?></p>
		</div>

		<div id='achinfo'>
			<p class='para-title font-cursive'>主要成就</p>
			<p class="text-info"><?php echo $data_person["ach"] ?></p>
		</div>


		<div id='workinfo'>
			<p class='para-title font-cursive'>作品信息</p>
		</div>
	</div>

	<div id="chartpane" class="chartbox">
		<div id="chart_1" class="chart"></div>
		<p style="height: 100px;"></p>
		<div id="chart_2" class="chart"></div>
		<p style="height: 100px;"></p>
		<div id="chart_3" class="chart"></div>
		<p style="height: 100px;"></p>
		<div id="chart_4" class="chart"></div>
		<p style="height: 100px;"></p>
		<div id="chart_5" class="chart"></div>
	</div>

	<script src="../JQMp3CoolListPlayer/js/jplayer.playlist.min.js"></script>
	<script src="../JQMp3CoolListPlayer/js/jquery.jplayer.min.js"></script>
<?php
	$music_flag=0;
	$music_list='';
	echo "<script  type='text/javascript'>";
	if(count($array_work)===0){
		echo "$('#masterTable').addClass('no-see');";
		echo "$('#workinfo').addClass('no-see');";
	};
	for($i=0; $i<count($array_work); $i++){
		$w_name=$array_work[$i]["w_name"];
		$w_time=$array_work[$i]["w_time"];
		$w_type=$array_work[$i]["w_type"];
		$w_pict=$array_work[$i]["w_pict"];
		$w_info=$array_work[$i]["w_info"];
		$w_info = str_replace(PHP_EOL, '', $w_info);
		
		$append_table='<tr><td><a style="cursor: pointer;color: #ffead2;" href="#work_'.$i.'">'.$w_name.'</a></td><td>'.$w_time.'</td><td>'.$w_type.'</td></tr>';
		echo "$('#masterBody').append('".$append_table."');";

		if($w_type=="音乐作品"){
			if($music_flag==0){
				$music = '<div class="music-player" style="margin: 50px auto;">'
				.'<div class="info" style="#003335;background: rgba(39, 2, 47, 0.68);">'
				.'  <div class="left">'
				.'	<a href="javascript:;" class="icon-shuffle"></a>'
				.'	<a href="javascript:;" class="icon-heart"></a>'
				.'  </div>'
				.'  <div class="center">'
				.'  <div class="jp-playlist">'
				.'	<ul>'
				.'	  <li></li>'
				.'	</ul>'
				.'  </div>'
				.'  </div>'
				.'  <div class="right">'
				.'	<a href="javascript:;" class="icon-repeat"></a>'
				.'	<a href="javascript:;" class="icon-share"></a>'
				.'  </div>'
				.'  <div class="progress jp-seek-bar">'
				.'	<span class="jp-play-bar" style="width: 0%;background: #8d08ad;"></span>'
				.'  </div>'
				.'</div>'
				.'<div class="controls" style="background: #129a8e;">'
				.'  <div class="current jp-current-time">00:00</div>'
				.'  <div class="play-controls">'
				.'	<a href="javascript:;" class="icon-previous jp-previous" title="previous"></a>'
				.'	<a href="javascript:;" class="icon-play jp-play" title="play"></a>'
				.'	<a href="javascript:;" class="icon-pause jp-pause" title="pause"></a>'
				.'	<a href="javascript:;" class="icon-next jp-next" title="next"></a>'
				.'  </div>'
				.'  <div class="volume-level jp-volume-bar">'
				.'	<span class="jp-volume-bar-value" style="width: 0%"></span>'
				.'	<a href="javascript:;" class="icon-volume-up jp-volume-max" title="max volume"></a>'
				.'	<a href="javascript:;" class="icon-volume-down jp-mute" title="mute"></a>'
				.'  </div>'
				.'</div>'
				.'<div id="jquery_jplayer" class="jp-jplayer"></div>'
				.'</div>';
				echo "$('#workinfo').append('".$music."');";
				$music_flag=1;
			}

			$music_list = $music_list.'{';
			$music_list = $music_list.'	title:"'.$w_name.'",';
			$music_list = $music_list.'	artist:"'.$data_person["name"].'",';
			$music_list = $music_list.'	mp3:"'.$w_pict.'"';
			$music_list = $music_list.'},';
			
			$append_1='';
			$append_2='<p id="work_'.$i.'" class="name font-cursive layui-elem-quote" style="font-size: 26px; background-color: transparent">'.$w_name.'</p>';
		}
		else{
			$append_1='<img class="layui-elem-quote" style="max-width: 400px; background-color: transparent" src="'.$w_pict.'">';
			$append_2='<p id="work_'.$i.'" class="name font-cursive" style="font-size: 26px;">'.$w_name.'</p>';
		}
		$append_3='<p class="text-info">'.$w_info.'</p>';
		$append_work=$append_1.$append_2.$append_3;
		echo "$('#workinfo').append('".$append_work."');";
	};
	if(count($array_work)!==0){
		echo 'var playlist = ['.$music_list.'];';
		echo 'var cssSelector = {';
		echo '  jPlayer: "#jquery_jplayer",';
		echo '  cssSelectorAncestor: ".music-player"';
		echo '};';
		echo 'var options = {';
		echo '  swfPath: "Jplayer.swf",';
		echo '  supplied: "ogv, m4v, oga, mp3"';
		echo '};';
		echo 'var myPlaylist = new jPlayerPlaylist(cssSelector, playlist, options);';
	}

	// Four infographics drawn using echarts
	echo 'var type_occu_chart = echarts.init(document.getElementById("chart_1"));
		$.post("chart/type_occu.php",function(info){
			info = JSON.parse(info);
			type_occu_chart.setOption({
				series:[{
					"name":"occu",
					"type":"pie",
					data:info,
					"color": ["#39375b","#745c97","#d597ce","#f5b0cb","#293462","#00818a","#ec9b3b","#f7be16"]
				}],
			})
		});
		var option_1 ={
			title: {
				text: "伟人类型比",
				left: "center"
			},
			legend : {
				orient: "vertical",
				x:"left",
			},
		};
		type_occu_chart.setOption(option_1);';

	echo 'var num_chart = echarts.init(document.getElementById("chart_2"));
			$.ajax({
				type:"get",
				async:false,
				url:"chart/num.php?act_age='.$data_person["act_age"].'",
				data:{},
				dataType:"json",
				success:function(info){
					num_chart.setOption({
						series:[{
							name:"act_age",
							"type":"pie",
							data:info,
							"color": ["#003f5c","#00918e","#4dd599","#ffdc34","#bc4873","#472b62","#bd574e"]
						}],
					})
				}
			});
			var option_2 ={
				title: {
				text: "时代人物比",
				left: "center"
				},
				legend : {
					orient: "vertical",
					x:"left",
				},
			};
	num_chart.setOption(option_2);';

	echo 'var act_gender_chart = echarts.init(document.getElementById("chart_3"));
	var men=[],women=[];
	var total_men=[],total_women=[];
	function arrTest_2(){
			$.ajax({
				type:"get",
				async:false,
				url:"chart/act_gender.php?act_age=\''.$data_person["act_age"].'\'",
				data:{},
				dataType:"json",
				success:function(result){
					if (result) {
						for (var i = 0; i < result.length; i++) {
							men.push(result[i].ratio_man_actage);
							women.push(result[i].ratio_women_actage);
						}
					}
				}
			})
			$.ajax({
				type:"get",
				async:false,
				url:"chart/gender.php",
				data:{},
				dataType:"json",
				success:function(result){
					if (result) {
						for (var i = 0; i < result.length; i++) {
							total_men.push(result[i].ratio_man);
							total_women.push(result[i].ratio_women);
						}
					}
				}
			})
		return total_men,total_women,men,women;
	}
	arrTest_2();
	var option_3 ={
		title: {
			text: "男女比",
			left: "center"
		},
		legend : {
			orient: "vertical",
			x:"left",
			data:["男(同时代)","女(同时代)","男","女"]
		},
		series : [
			{
				"name":"act_gender",
				"type":"pie",
				"radius": ["0", "35%"],
				"data":[{value:men[0],name:"男(同时代)"},
						{value:women[0],name:"女(同时代)"}],
				"color": ["#5e4782","#d597ce"]

			},
			{
				"name":"total_gender",
				"type":"pie",
				"radius": ["35%", "75%"],
				"data":[{value:total_men[0],name:"男"},
						{value:total_women[0],name:"女"}],
				"color": ["#39375b","#745c97"]
			}
		]
	};
	act_gender_chart.setOption(option_3);';

	echo 'var bar_chart_1 = echarts.init(document.getElementById("chart_4"));
	var  bar_chart_2 =echarts.init(document.getElementById("chart_5"));

	var act_avg_age=[];
	var age=[];
	var act_avg_write=[];
	var write=[];
	
	function arrTest_3(){
			$.ajax({
				type:"get",
				async:false,
				url:"chart/bar.php?act_age=\''.$data_person["act_age"].'\'&name=\''.$data_person["name"].'\'",
				data:{},
				dataType:"json",
				success:function(result){
					if (result) {
						for (var i = 0; i < result.length; i++) {
							act_avg_age.push(result[i].act_avg_age);
							age.push(result[i].age);
							act_avg_write.push(result[i].act_avg_write);
							write.push(result[i].write);
						}
					}
				}
			})
		return act_avg_age,age,write,act_avg_write;
	}
	arrTest_3();

	var option_4 ={
		title: {
			text: "时代平均年龄与个人",
			left: "center"
		},
		grid:{
			x:100,
			y:100,
			width:500,
			height:300,
		},
		legend : {
			orient:"verticle",
			x:"left",
			data:["年龄","时代平均年龄"]
		},
		xAxis:{
			type:"category",
			data:["年龄"]
		},
		yAxis:[{
			
			type:"value",
			name:"年龄",
			min: 0,
			max: 100,
			position:"left",
		}],
		series : [
			{
				"name":"年龄",
				"type":"bar",
				"data":[{value:age[0],name:"年龄"}],
				"color": ["#8ac6d1"]

			},
			{
				"name":"时代平均年龄",
				"type":"bar",
				"data":[{value:act_avg_age[0],name:"时代平均年龄"}],
				"color": ["#bbded6"]
			},
		]
	};
	bar_chart_1.setOption(option_4);
	var option_5 ={
		title: {
			text: "时代平均作品数与个人",
			left: "center"
		},
		grid:		{
			x:100,
			y:100,
			width:500,
			height:300,
		},
		legend : {
			orient:"verticle",
			x:"left",
			data:["作品数","时代平均作品数"]
		},
		xAxis:{
			type:"category",
			data:["作品"]
		},
		yAxis:[{
			type:"value",
			name:"作品数",
			min: 0,
			max: 6,
			position:"left",
		}],
		series : [
			{
				"name":"作品数",
				"type":"bar",
				"data":[{value:write[0],name:"作品数"}],
				"color": ["#ffb6b9"]

			},
			{
				"name":"时代平均作品数",
				"type":"bar",
				"data":[{value:act_avg_write[0],name:"时代平均作品数"}],
				"color": ["#fae3d9"]

			},
		]
	};
	bar_chart_2.setOption(option_5);';

	echo "</script>";
?>

</body>
</html>